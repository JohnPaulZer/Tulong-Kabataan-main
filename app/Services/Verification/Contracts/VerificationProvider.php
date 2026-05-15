<?php

namespace App\Services\Verification\Contracts;

/**
 * Common contract every ID verification provider must satisfy. The
 * orchestrator only talks to this interface so we can swap providers
 * (Didit / OCR.Space / Google Vision / future) by changing one env var.
 *
 * Returned shape:
 * [
 *   'success'         => bool,
 *   'provider'        => string,                          // provider key
 *   'reference_id'    => string|null,                     // remote id, if any
 *   'raw_text'        => string,                          // OCR text dump
 *   'extracted'       => [
 *       'full_name'        => ?string,
 *       'birthdate'        => ?string,                    // ISO yyyy-mm-dd
 *       'id_number'        => ?string,
 *       'address'          => ?string,
 *       'expiration_date'  => ?string,                    // ISO yyyy-mm-dd
 *       'sex'              => ?string,
 *       'nationality'      => ?string,
 *       'id_type_detected' => ?string,                    // philid|drivers_license|null
 *   ],
 *   'authenticity'    => [                                // optional, KYC providers only
 *       'verified'  => ?bool,
 *       'liveness'  => ?bool,
 *       'face_match' => ?bool,
 *       'score'     => ?float, // 0-1
 *   ],
 *   'raw'             => array|null,                      // full raw payload, kept for audit
 *   'error'           => ?string,                         // technical error, never shown to user
 * ]
 */
interface VerificationProvider
{
    public function name(): string;

    public function isConfigured(): bool;

    public function supportsAuthenticity(): bool;

    /**
     * Send the front (and optionally back) of an ID for processing.
     */
    public function verify(string $frontAbsolutePath, ?string $backAbsolutePath = null, array $context = []): array;
}
