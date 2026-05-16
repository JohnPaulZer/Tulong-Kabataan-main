@extends('errors.layout')

@section('code', '504')
@section('title', 'Gateway Timeout')
@section('message', 'A gateway or proxy waited too long for the application server to respond.')
@section('hint', 'Slow database queries, external APIs, or long-running work can cause this status code.')
@section('primary_tip', 'The upstream service did not answer before the gateway timeout expired.')
@section('secondary_tip', 'Try again later. If you manage the site, check slow jobs, API calls, and timeout settings.')
