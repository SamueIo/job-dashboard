<?php

namespace App\Jobs;

use App\Models\Email;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Event;


class ClassifyEmailJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public ?int $email_id = null)
    {
    }

    public function handle(): void
    {
        if (!$this->email_id) {
            Log::error('Missing email_id in job');
            return;
        }

        $email = Email::find($this->email_id);

        if (!$email) {
            Log::error('Email not found', [
                'email_id' => $this->email_id
            ]);

            return;
        }
        $emailText = $email->body ?: $email->snippet;

        $prompt = "
You are an AI assistant for job recruitment emails.

Analyze the email and extract structured information.

Return ONLY valid JSON.

Date format must be ISO 8601.

Example:
{
  'start_at': '2026-02-06T12:00:00'
}

Possible statuses:
- applied
- interview
- rejected
- offer
- other

Possible priorities:
- high
- medium
- low

JSON format:
{
  \"status\": \"\",
  \"priority\": \"\",
  \"company\": \"\",
  \"role\": \"\",
  \"stage\": \"\",
  \"interview_at\": \"\",
  \"meeting_link\": \"\",
  \"summary\": \"\",
  \"confidence\": 0
}

Rules:
- summary must be short and professional
- confidence must be between 0 and 1
- if information is missing use null
- return ONLY valid JSON
- do not include markdown
- do not explain anything

Priority rules:
- HIGH = interviews, offers, assignments, urgent replies, deadlines
- MEDIUM = recruiter updates, confirmations, follow-ups
- LOW = newsletters, promotions, automated emails, irrelevant updates

Email:
Subject: {$email->subject}

From: {$email->from}

Text:
{$emailText}
";

        try {
Log::info('Cleaned email chars', [
    'email_id' => $email->id,
    'chars' => strlen($emailText),
]);
            $response = Http::timeout(180)->post(
                'http://localhost:11434/api/generate',
                [
                    'model' => 'llama3.1:latest',
                    'prompt' => $prompt,
                    'stream' => false,
                    'format' => 'json',
                    'options' => [
                        'temperature' => 0,
                    ],
                ]
            );

            if (!$response->successful()) {
                Log::error('Ollama request failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return;
            }

            $data = $response->json();

            $content = $data['response'] ?? '{}';

            $parsed = json_decode($content, true);

        if (!$parsed) {
            Log::error('Invalid JSON returned from Ollama', [
                'response' => $content
            ]);

            return;
        }

            $status = strtolower(trim($parsed['status'] ?? 'other'));
            $priority = strtolower(trim($parsed['priority'] ?? 'medium'));

            // Auto fallback for status 
            $stage = strtolower(trim($parsed['stage'] ?? ''));

            $allowedStatuses = [
                'applied',
                'interview',
                'rejected',
                'offer',
                'other'
            ];

            $allowedPriorities = [
                'high',
                'medium',
                'low',
            ];


            $text = strtolower($emailText);

            if (!in_array($status, $allowedStatuses)) {
                $status = 'other';
            }
            if (!in_array($priority, $allowedPriorities)) {
                $priority = 'medium';
            }
            $email->update([
                'status' => $status,
                'priority' => $priority,
                'company' => $parsed['company'] ?? null,
                'role' => $parsed['role'] ?? null,
                'stage' => $parsed['stage'] ?? null,
                'interview_at' => $parsed['interview_at'] ?? null,
                'meeting_link' => $parsed['meeting_link'] ?? null,
                'summary' => $parsed['summary'] ?? null,
                'confidence' => $parsed['confidence'] ?? null,
            ]);
            $this->createEvent($email);

            Log::info('Email classified', [
                'email_id' => $email->id,
                'priority' => $parsed['priority'] ?? null,
                'status' => $parsed['status'] ?? null,
                'content' => $content
            ]);


        } catch (\Throwable $e) {

            Log::error('AI classification failed', [
                'email_id' => $this->email_id,
                'message' => $e->getMessage(),
            ]);
        }
    }
    public function createEvent(Email $email)
    {
        if (
            $email->status === 'interview' &&
            $email->interview_at
        ) {
                    
            Event::firstOrCreate([
                    
                'user_id' => $email->user_id,
                'email_id' => $email->id,
                    
                'title' => 'Interview',
                'company' => $email->company,
                    
                'start_at' => $email->interview_at,
                    
                'type' => 'interview',
            ]);
        }
    }
}