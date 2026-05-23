<?php

namespace App\Jobs;

use App\Models\Email;
use App\Models\Event;

use Carbon\Carbon;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

use Soundasleep\Html2Text;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ClassifyEmailJob implements ShouldQueue
{
    use Queueable;

    /*
    |--------------------------------------------------------------------------
    | Queue Settings
    |--------------------------------------------------------------------------
    */

    public $tries = 1;

    public $timeout = 180;

    /*
    |--------------------------------------------------------------------------
    | Constructor
    |--------------------------------------------------------------------------
    */

    public function __construct(
        public ?int $email_id = null
    ) {}

    /*
    |--------------------------------------------------------------------------
    | Handle
    |--------------------------------------------------------------------------
    */

    public function handle(): void
    {
        if (!$this->email_id) {

            Log::error('Missing email_id in job');

            return;
        }

        $email = Email::find($this->email_id);

        if (!$email) {

            Log::error('Email not found', [
                'email_id' => $this->email_id,
            ]);

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Prevent re-processing
        |--------------------------------------------------------------------------
        */

        if ($email->ai_status === 'completed') {

            Log::info('Email already classified', [
                'email_id' => $email->id,
            ]);

            return;
        }

        $email->update([
            'ai_status' => 'processing',
        ]);

        /*
        |--------------------------------------------------------------------------
        | EMAIL SOURCE
        |--------------------------------------------------------------------------
        */

        $emailText = $email->body
            ?: $email->snippet
            ?: '';

        /*
        |--------------------------------------------------------------------------
        | HTML -> TEXT
        |--------------------------------------------------------------------------
        */

        libxml_use_internal_errors(true);

        try {

            $emailText = Html2Text::convert($emailText);

        } catch (\Throwable $e) {

            Log::warning('Html2Text failed', [
                'email_id' => $email->id,
                'message' => $e->getMessage(),
            ]);

            $emailText = strip_tags($emailText);

        } finally {

            libxml_clear_errors();
        }

        /*
        |--------------------------------------------------------------------------
        | CLEANUP
        |--------------------------------------------------------------------------
        */

        $emailText = html_entity_decode($emailText);

        $emailText = preg_replace(
            "/\r\n|\r/",
            "\n",
            $emailText
        );

        // remove urls
        $emailText = preg_replace(
            '/https?:\/\/\S+/i',
            '',
            $emailText
        );

        // remove emails
        $emailText = preg_replace(
            '/\S+@\S+\.\S+/',
            '',
            $emailText
        );

        // remove cid
        $emailText = preg_replace(
            '/\[cid:.*?\]/i',
            '',
            $emailText
        );

        // remove unsubscribe
        $emailText = preg_replace(
            '/unsubscribe.*/is',
            '',
            $emailText
        );

        // remove legal footer
        $emailText = preg_replace(
            '/this email.*confidential.*/is',
            '',
            $emailText
        );

        // remove reply chains
        $emailText = preg_replace(
            '/On\s.+?wrote:(.*)/is',
            '',
            $emailText
        );

        // remove forwarded messages
        $emailText = preg_replace(
            '/-----Original Message-----.*/is',
            '',
            $emailText
        );

        // normalize spaces
        $emailText = preg_replace(
            '/[ \t]+/',
            ' ',
            $emailText
        );

        // normalize line breaks
        $emailText = preg_replace(
            "/\n{3,}/",
            "\n\n",
            $emailText
        );

        $emailText = trim($emailText);

        /*
        |--------------------------------------------------------------------------
        | IMPORTANT:
        | Keep beginning + ending
        |--------------------------------------------------------------------------
        */

        if (strlen($emailText) > 2200) {

            $start = substr($emailText, 0, 1200);

            $end = substr($emailText, -800);

            $emailText = $start .
                "\n\n[EMAIL TRUNCATED]\n\n" .
                $end;
        }

        /*
        |--------------------------------------------------------------------------
        | PROMPT
        |--------------------------------------------------------------------------
        */

        $prompt = <<<PROMPT
You are an AI assistant for recruitment emails.

Analyze the email carefully.

Return ONLY valid JSON.

Allowed statuses:
- applied
- interview
- rejected
- offer
- other

Allowed priorities:
- high
- medium
- low

Priority rules:
- HIGH = interviews, offers, coding tasks, deadlines, urgent replies
- MEDIUM = recruiter updates, follow-ups, confirmations
- LOW = newsletters, ads, marketing, automated emails

Rules:
- summary max 160 chars
- confidence must be decimal between 0 and 1
- if unknown use null
- no markdown
- no explanations
- only valid JSON

JSON format:
{
  "status": "",
  "priority": "",
  "company": "",
  "role": "",
  "stage": "",
  "interview_at": null,
  "meeting_link": null,
  "summary": "",
  "confidence": 0.0
}

Subject:
{$email->subject}

From:
{$email->from}

Email:
{$emailText}
PROMPT;

        Log::info('AI classification started', [
            'email_id' => $email->id,
            'chars' => strlen($emailText),
        ]);

        /*
        |--------------------------------------------------------------------------
        | OLLAMA
        |--------------------------------------------------------------------------
        */

        try {

            $response = Http::timeout(90)
                ->connectTimeout(15)
                ->post('http://localhost:11434/api/generate', [

                    'model' => 'llama3.1:latest',

                    'prompt' => $prompt,

                    'stream' => false,

                    'format' => 'json',

                    'options' => [

                        'temperature' => 0,

                        'num_predict' => 120,

                        'top_p' => 0.1,
                    ],
                ]);

            if (!$response->successful()) {

                $email->update([
                    'ai_status' => 'failed',
                ]);

                Log::error('Ollama request failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return;
            }

            $data = $response->json();

            $content = $data['response'] ?? '{}';

            Log::info('Raw AI response', [
                'email_id' => $email->id,
                'response' => $content,
            ]);

            $parsed = json_decode($content, true);

            if (!$parsed || !is_array($parsed)) {

                $email->update([
                    'ai_status' => 'failed',
                ]);

                Log::error('Invalid JSON returned', [
                    'email_id' => $email->id,
                    'response' => $content,
                ]);

                return;
            }

            /*
            |--------------------------------------------------------------------------
            | VALIDATION
            |--------------------------------------------------------------------------
            */

            $status = strtolower(
                trim($parsed['status'] ?? 'other')
            );

            $priority = strtolower(
                trim($parsed['priority'] ?? 'medium')
            );

            $allowedStatuses = [
                'applied',
                'interview',
                'rejected',
                'offer',
                'other',
            ];

            $allowedPriorities = [
                'high',
                'medium',
                'low',
            ];

            if (!in_array($status, $allowedStatuses)) {
                $status = 'other';
            }

            if (!in_array($priority, $allowedPriorities)) {
                $priority = 'medium';
            }

            /*
            |--------------------------------------------------------------------------
            | CONFIDENCE FIX
            |--------------------------------------------------------------------------
            */

            $confidence = $parsed['confidence'] ?? 0;

            if (!is_numeric($confidence)) {
                $confidence = 0;
            }

            $confidence = (float) $confidence;

            // Fix percentages like 50 instead of 0.5
            if ($confidence > 1) {
                $confidence = $confidence / 100;
            }

            $confidence = min(
                max($confidence, 0),
                1
            );

            /*
            |--------------------------------------------------------------------------
            | FALLBACK PRIORITY
            |--------------------------------------------------------------------------
            */

            $text = strtolower(
                $email->subject . ' ' . $emailText
            );

            if (
                str_contains($text, 'interview') ||
                str_contains($text, 'assessment') ||
                str_contains($text, 'deadline') ||
                str_contains($text, 'offer')
            ) {
                $priority = 'high';
            }

            /*
            |--------------------------------------------------------------------------
            | UPDATE EMAIL
            |--------------------------------------------------------------------------
            */

            $email->update([

                'status' => $status,

                'priority' => $priority,

                'company' => $parsed['company'] ?? null,

                'role' => $parsed['role'] ?? null,

                'stage' => $parsed['stage'] ?? null,

                'interview_at' => $this->parseDate(
                    $parsed['interview_at'] ?? null
                ),

                'meeting_link' => $parsed['meeting_link'] ?? null,

                'summary' => Str::limit(
                    trim($parsed['summary'] ?? ''),
                    160
                ),

                'confidence' => $confidence,

                'ai_status' => 'completed',
            ]);

            /*
            |--------------------------------------------------------------------------
            | CREATE EVENT
            |--------------------------------------------------------------------------
            */

            $this->createEvent($email);

            Log::info('Email classified successfully', [

                'email_id' => $email->id,

                'status' => $status,

                'priority' => $priority,

                'confidence' => $confidence,
            ]);

        } catch (\Throwable $e) {

            $email->update([
                'ai_status' => 'failed',
            ]);

            Log::error('AI classification failed', [

                'email_id' => $this->email_id,

                'message' => $e->getMessage(),
            ]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Failed
    |--------------------------------------------------------------------------
    */

    public function failed(\Throwable $e): void
    {
        if (!$this->email_id) {
            return;
        }

        Email::where('id', $this->email_id)
            ->update([
                'ai_status' => 'failed',
            ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Create Event
    |--------------------------------------------------------------------------
    */

    public function createEvent(Email $email): void
    {
        if (
            $email->status !== 'interview' ||
            !$email->interview_at
        ) {
            return;
        }

        Event::firstOrCreate([

            'user_id' => $email->user_id,

            'email_id' => $email->id,

        ], [

            'title' => 'Interview',

            'company' => $email->company,

            'start_at' => $email->interview_at,

            'end_at' => Carbon::parse(
                $email->interview_at
            )->addHour(),

            'type' => 'interview',

            'source' => 'local',

            'category' => 'interview',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Parse Date
    |--------------------------------------------------------------------------
    */

    private function parseDate(?string $date): ?string
    {
        if (!$date) {
            return null;
        }

        try {

            return Carbon::parse($date)
                ->toDateTimeString();

        } catch (\Throwable $e) {

            return null;
        }
    }
}