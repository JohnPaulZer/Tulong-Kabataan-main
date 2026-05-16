@extends('errors.layout')

@section('code', '429')
@section('title', 'Too Many Requests')
@section('message', 'Too many requests were sent in a short amount of time.')
@section('hint', 'Rate limits help protect the website from accidental repeats, bots, and heavy traffic spikes.')
@section('primary_tip', 'The application temporarily slowed this request down to protect the service.')
@section('secondary_tip', 'Wait a moment before trying again, and avoid repeatedly clicking the same action.')
