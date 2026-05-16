@extends('errors.layout')

@section('code', '404')
@section('title', 'Not Found')
@section('message', 'The page you are looking for does not exist, was moved, or is no longer available.')
@section('hint', 'The address may be misspelled, the route may not exist, or the content may have been removed.')
@section('primary_tip', 'Laravel could not match this URL to a page, route, campaign, event, or resource.')
@section('secondary_tip', 'Check the URL, go back to the previous page, or start again from the homepage.')
