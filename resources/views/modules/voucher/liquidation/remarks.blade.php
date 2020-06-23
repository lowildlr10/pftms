<div class="row">
    <div class="col-md-12">
        <button class="btn btn-sm btn-block btn-link blue-text"
                onclick="$(this).refreshRemarks('{{ route('ca-lr-show-remarks', ['id' => $id]) }}');">
                <i class="fas fa-sync"></i> Refresh
        </button>
    </div>
</div>

<div class="table-responsive border m-0 p-0" style="height: 350px;">
    <table class="table table-sm m-0 p-0">
        @if (count($docRemarks) > 0)
            @foreach ($docRemarks as $itemCtr => $item)
                @if (!empty($item->remarks))
        <tr>
            <td>
                <div class="border rounded p-3 z-depth-1 {{ $item->emp_from == Auth::user()->id ?
                     'ml-5 grey lighten-5' : 'mr-5 rgba-blue-slight' }}">
                    <small class="font-weight-bold">
                        {{ $item->emp_from == Auth::user()->id ? 'You' :
                        Auth::user()->getEmployee($item->emp_from)->name }}
                    </small><br>
                    {{ $item->remarks }}<br><br>
                    <small class="grey-text">
                        <i class="far fa-calendar-alt"></i> {{ $item->logged_at }}
                    </small>
                </div>
            </td>
        </tr>
                @endif
            @endforeach
        @endif
    </table>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="md-form">
            <textarea id="message" class="md-textarea form-control required"
                      name="message" rows="3"></textarea>
            <label for="message">
                <i class="fas fa-comment-dots"></i> Type your message here
            </label>
        </div>
        <button class="btn btn-sm btn-block btn-outline-mdb-color"
                onclick="$(this).storeRemarks('{{ route('ca-lr-store-remarks', ['id' => $id]) }}',
                                              '{{ route('ca-lr-show-remarks', ['id' => $id]) }}');">
            <i class="fas fa-location-arrow"></i> Send
        </button>
    </div>
</div>
