<div id="{{ $modalId }}" class="modal fade" role="dialog" aria-modal="true" tabindex="-1"
    aria-labelledby="{{ $modalId }}-title">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('adviceNotes.destroy', ['adviceNote' => $item->advice_note_id]) }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h2 class="modal-title h4" id="{{ $modalId }}-title">Cancel Advice Note</h2>
                </div>
                <div class="modal-body">
                    <p>Cancel advice note <strong>{{ $item->advice_note_id }}</strong>?
                        @if (filled($item->control_number))
                            Control number {{ $item->control_number }}.
                        @endif
                        This action cannot be undone.
                    </p>
                    <div class="form-group">
                        <label for="{{ $modalId }}-reason">Reason for cancellation</label>
                        <textarea id="{{ $modalId }}-reason" name="reasons" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Keep advice note</button>
                    <button type="submit" class="btn btn-danger">Cancel Advice Note</button>
                </div>
            </form>
        </div>
    </div>
</div>
