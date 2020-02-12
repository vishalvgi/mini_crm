@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{__('message.companies')}}</div>

                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                    @endif
                    <div class="container">
                        <div class="row">
                            <div class="col-md-10"></div>
                            <div class="col-md-2"><a class="btn btn-primary" href="/home/create">{{__('message.add')}}</a></div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">{{__('message.name')}}</th>
                                            <th scope="col">{{__('message.email')}}</th>
                                            <th scope="col">{{__('message.logo')}}</th>
                                            <th scope="col">{{__('message.website')}}</th>
                                            <th scope="col">{{__('message.action')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($view_data as $single)
                                        <tr>
                                            <td>{{ $single->name }}</td>
                                            <td>{{ $single->email }}</td>
                                            <td>
                                                @if($single->logo)
                                                <img src="/storage/public/{{ $single->logo }}" height="100px" width="100px" />
                                                @endif
                                            </td>
                                            <td>{{ $single->website }}</td>
                                            <td>
                                                <form action="/home/{{$single->id}}" method = "post" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button  class="btn btn-link" type="submit">{{__('message.delete')}}</button>
                                                </form>/&nbsp;&nbsp; <a href="company/{{$single->id}}/employee">
                                                    {{__('message.employees')}}
                                                </a> &nbsp;&nbsp;/ &nbsp;&nbsp;
                                                <a href="/home/{{$single->id}}/edit" style="">
                                                    {{__('message.edit')}}
                                                </a> 
                                            </td>
                                        </tr>
                                        @endforeach
                                        @if(!count($view_data))
                                        <tr><td colspan="5" style="text-align: center">{{__('message.no_data_exist')}}</td></tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    {{ $view_data->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
