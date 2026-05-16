@extends('errors.layout')

@section('code', isset($exception) && method_exists($exception, 'getStatusCode') ? (string) $exception->getStatusCode() : '5xx')
@section('title', 'Server Error')
@section('message', 'The server could not complete this request because something failed on the application side.')
@section('hint', 'This kind of error is usually temporary, but repeated failures should be investigated in the server logs.')
@section('primary_tip', 'The request reached the server, but the server could not finish the response.')
@section('secondary_tip', 'Try again later. Site maintainers should review logs, services, and recent deployments.')
