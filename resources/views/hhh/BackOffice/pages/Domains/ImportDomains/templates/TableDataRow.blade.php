@php
    $DomainsTableEnum = App\Enums\Database\Tables\DomainsTableEnum::class;

    $DomainCategoryName = 'DomainCategoryName';

    // The order of fileds must be set by table "thead" order
    $fields = [$DomainsTableEnum::Name->dbName(), $DomainsTableEnum::Status->dbName(), $DomainsTableEnum::AutoRenew->dbName(), $DomainsTableEnum::RegisteredAt->dbName(), $DomainsTableEnum::ExpiresAt->dbName(), $DomainsTableEnum::AnnouncedAt->dbName(), $DomainsTableEnum::BlockedAt->dbName(), $DomainsTableEnum::Descr->dbName()];
@endphp

<table>
    <tr id="TableDataRowTemplate">

        <td>index_value</td> {{-- Row index --}}

        @foreach ($fields as $field)
            <td name="{{ $field }}" style="direction: ltr;">{{ $field }}_value</td>
        @endforeach

        <td id="DataRowReslut_index_value" class="text-danger"></td> {{-- Row index --}}

    </tr>
</table>

<input type="hidden" id="TableFieldNames" value="{{ json_encode($fields) }}">
