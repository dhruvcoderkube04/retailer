@extends('layouts.email-base')

@section('content')
    <div style="font-family: Arial, sans-serif; font-size: 14px; line-height: 1.6;">
        <p>
            The product CSV file that you started importing at <strong>{{ $importStartDateTime }}</strong> is now completed.
        </p>

        <p>
            Below are the results:
        </p>

        <table cellpadding="8" cellspacing="0" border="0" style="width: 100%; border-collapse: collapse;">
            @if (count($validRows))
                <tr>
                    <td style="background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb;">
                        <strong>{{ count($validRows) }}</strong> product(s) imported successfully.
                    </td>
                </tr>
            @endif
            @if (count($invalidRows))
                <tr>
                    <td style="background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb;">
                        <strong>{{ count($invalidRows) }}</strong> product(s) failed to import.
                    </td>
                </tr>
            @endif
        </table>

        @if (count($invalidRows))
            <h4 style="margin-top: 10px; color: #dc3545;">Ignored/Not Imported Products:</h4>
            <ul style="padding-left: 20px;">
                @foreach ($invalidRows as $msg)
                    <li>{{ $msg }}</li>
                @endforeach
            </ul>
        @endif

        <p style="margin-top: 30px;">
            If you have any questions or need help fixing these issues, please contact support.
        </p>
    </div>
@endsection
