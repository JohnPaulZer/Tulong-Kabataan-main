@extends('errors.layout')

@section('code', '400')
@section('title', 'Bad Request')
@section('message', 'The request could not be processed because something sent to the server was invalid.')
@section('hint', 'This can happen when a form, link, upload, or API request contains missing or malformed information.')
@section('primary_tip', 'The browser or client sent data that the application could not understand.')
@section('secondary_tip', 'Check the form fields, refresh the page, and submit the request again.')
