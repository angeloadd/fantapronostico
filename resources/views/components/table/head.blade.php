<thead>
    <tr class="[&>*]:border-transparent bg-base-100 [&>*]:text-center">
        @foreach($heads as $head)
            <th
                @class($head['class'] ?? [])
            >
                {!! $head['text'] !!}
            </th>
        @endforeach
    </tr>
</thead>
