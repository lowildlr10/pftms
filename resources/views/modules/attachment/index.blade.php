@if (count($attachments) > 0)
    <div id="tree-attachments" class="treeview-animated border mb-3">
        <h6 class="pt-3 pl-3">{{ $parentID }}</h6>
        <hr>
        <ul class="treeview-animated-list mb-3" style="list-style-type: none;">
            @foreach ($attachments as $key => $attachment)
            <li id="attachment-{{ $key }}">
                <div class="treeview-animated-element">
                    <a href="{{ url($attachment->directory) }}" class="dark-grey-text" target="_blank">
                        <i class="fas fa-file-pdf"></i> {{ basename(url($attachment->directory)) }}
                    </a>
                    <a href="#" class="btn btn-link btn-sm btn-rounded border p-0 px-1 mb-2"
                       onclick="$(this).deleteAttachment('{{ $attachment->id }}',
                                                         '#attachment-{{ $key }}',
                                                         '{{ $attachment->directory }}');">
                        <i class="fas fa-minus-circle red-text"></i> Remove
                    </a>
                </div>
            </li>
            @endforeach
        </ul>
    </div>
@else
    <div class="treeview-animated border mb-3">
        <h6 class="pt-3 pl-3">{{ $parentID }}</h6>
        <hr>
        <ul class="treeview-animated-list mb-3" style="list-style-type: none;">
            <li>
                <div class="treeview-animated-element">
                    <h6 class="red-text">No attachment found.</h6>
                </div>
            </li>
        </ul>
    </div>
@endif
