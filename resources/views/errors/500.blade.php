@extends('errors.layout')

@section('code', '500')
@section('title', 'Server Error')
@section('message', 'The application ran into an unexpected problem while processing this request.')
@section('hint', 'This is usually temporary, but the server logs should be checked if it keeps happening.')
@section('primary_tip', 'Something inside the application failed before the request could be completed.')
@section('secondary_tip', 'Try again later. If you manage the site, check the Laravel logs and recent changes.')
