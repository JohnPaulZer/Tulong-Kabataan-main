@extends('errors.layout')

@section('code', '401')
@section('title', 'Unauthorized')
@section('message', 'You need to sign in before this page or request can continue.')
@section('hint', 'Your session may have expired, or the request may be missing valid login credentials.')
@section('primary_tip', 'The application could not confirm who is making this request.')
@section('secondary_tip', 'Sign in again, then return to the page you were trying to open.')
