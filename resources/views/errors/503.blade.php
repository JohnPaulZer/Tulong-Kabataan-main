@extends('errors.layout')

@section('code', '503')
@section('title', 'Service Unavailable')
@section('message', 'The service is temporarily unavailable or under maintenance.')
@section('hint', 'The website may be deploying updates, handling heavy traffic, or waiting on a required service.')
@section('primary_tip', 'The server is not ready to handle this request right now.')
@section('secondary_tip', 'Try again shortly. If maintenance is active, wait until it is finished.')
