@extends('errors.layout')

@section('code', isset($exception) && method_exists($exception, 'getStatusCode') ? (string) $exception->getStatusCode() : '4xx')
@section('title', 'Request Error')
@section('message', 'The request could not be completed because something about it needs attention.')
@section('hint', 'This usually means the URL, session, permissions, or submitted information should be checked.')
@section('primary_tip', 'The server received the request but could not complete it as sent.')
@section('secondary_tip', 'Review the page address or form details, then try again.')
