@extends('errors.layout')

@section('code', '502')
@section('title', 'Bad Gateway')
@section('message', 'A gateway or proxy could not get a valid response from the application server.')
@section('hint', 'This can happen when an upstream server is down, overloaded, or misconfigured.')
@section('primary_tip', 'The request reached a gateway, but the service behind it did not respond correctly.')
@section('secondary_tip', 'Check server health, proxy settings, ports, DNS, SSL, and application logs.')
