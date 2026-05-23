export type Appearance =
    | 'light'
    | 'dark'
    | 'blue'
    | 'system';

export type ResolvedAppearance =
    | 'light'
    | 'dark'
    | 'blue';
export type AppVariant = 'header' | 'sidebar';

export type FlashToast = {
    type: 'success' | 'info' | 'warning' | 'error';
    message: string;
};
