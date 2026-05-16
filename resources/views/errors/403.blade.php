@extends('errors.layout')

@section('code', '403')
@section('title', 'Forbidden')
@section('message', 'You are signed in, but this account does not have permission to open this page.')
@section('hint', 'Access may be limited to an organizer, administrator, owner, or approved user.')
@section('primary_tip', 'The server understood the request but blocked access for this account.')
@section('secondary_tip', 'Use an account with the right permission or return to a public page.')
