@extends('layout')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/main.css') }}" type="text/css" />
@endsection

@section('content')
    <section class="w-full">
        <div class="w-full mb-8 rounded-lg shadow-lg">
            <div class="w-full overflow-x-scroll lg:overflow-hidden">
                <table class="w-full">
                    <thead>
                        <tr class="text-sm font-semibold tracking-wide text-left text-gray-900 bg-gray-100 border-b border-gray-600">
                            <th class="px-4 py-3">Name</th>
                            <th class="px-4 py-3">Client Email</th>
                            <th class="px-4 py-3">Client Phone</th>
                            <th class="px-4 py-3">Coupon</th>
                            <th class="px-4 py-3">Date</th>
                        </tr>
                    </thead>
                    @foreach($results as $result)
                        <tbody class="bg-white">
                            <tr class="text-gray-700">
                                <td class="px-4 py-3 text-sm border">{{ $result->client->name }}</td>
                                <td class="px-4 py-3 text-sm border">{{ $result->client->email }}</td>
                                <td class="px-4 py-3 text-sm border">{{ $result->client->phone }}</td>
                                <td class="px-4 py-3 text-sm border">{{ $result->code }}</td>
                                <td class="px-4 py-3 text-sm border">{{ $result->created_at->format('Y-m-d') }}</td>
                            </tr>
                        </tbody>
                    @endforeach
                </table>
            </div>
            {{ $results->links() }}
        </div>
    </section>
@endsection