@extends('errors.layout')

@section('code', '408')
@section('title', 'Request Timeout')
@section('message', 'The server stopped waiting because the request took too long to finish.')
@section('hint', 'This can happen on a slow connection, during a large upload, or after leaving a page idle.')
@section('primary_tip', 'The request did not reach the server completely within the expected time.')
@section('secondary_tip', 'Refresh the page, check your connection, and try again with a smaller upload if needed.')
