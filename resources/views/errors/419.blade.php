@extends('errors.layout')

@section('code', '419')
@section('title', 'Page Expired')
@section('message', 'The page session expired before the request could be verified.')
@section('hint', 'Laravel shows this when a form token is missing, expired, or no longer matches your session.')
@section('primary_tip', 'The security token for this form or request is no longer valid.')
@section('secondary_tip', 'Refresh the page, sign in again if needed, and resubmit the form.')
