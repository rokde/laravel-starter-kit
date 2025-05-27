export interface Passkey {
    id: string;
    name: string;
    last_used_at: string | null;
}

export interface PasskeyOptions {
    extensions: {
        extensions: unknown[];
    };
    challenge: string;
    timeout: number | null;
    rp: {
        icon: null;
        id: string;
        name: string;
    };
    user: {
        displayName: string;
        icon: null;
        id: number;
        name: string;
    };
    pubKeyCredParams: unknown[];
    authenticatorSelection: null;
    attestation: null;
    excludeCredentials: null;
}
