{{-- resources/views/warehouse/partials/batch-issue-modal.blade.php --}}
<div class="modal fade" id="batchIssueModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="{{ route('warehouse.issue-batch') }}" id="batchIssueForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-box-arrow-right text-info"></i> Видача зі складу</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @if($errors->has('items'))
                    <div class="alert alert-danger py-2">{{ $errors->first('items') }}</div>
                    @endif

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Філія <span class="text-danger">*</span></label>
                            <select name="destination_branch_id" id="destinationBranchId" class="form-select" required>
                                <option value="">— Оберіть філію —</option>
                                @foreach($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Номер кабінету <span class="text-danger">*</span></label>
                            <input type="text" name="destination_room_number" id="destinationRoomNumber" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Кому видано</label>
                            <input type="text" name="issued_to" class="form-control" placeholder="ПІБ отримувача (необов'язково)">
                        </div>
                    </div>

                    <hr>

                    <h6 class="mb-2"><i class="bi bi-list-ul"></i> Товари для видачі</h6>
                    <div id="batchItemsContainer"></div>
                    <button type="button" class="btn btn-sm btn-outline-success mt-2" id="addBatchItemBtn">
                        <i class="bi bi-plus"></i> Додати товар
                    </button>

                    <hr>

                    <p class="mb-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#replacementSection">
                            <i class="bi bi-arrow-repeat"></i> Заміна обладнання (необов'язково)
                        </button>
                    </p>
                    <div class="collapse" id="replacementSection">
                        <div class="card card-body bg-light">
                            <p class="small text-muted mb-2">Якщо це видача замінює обладнання, яке вже є в кабінеті — оберіть його та вкажіть, куди його діти.</p>
                            <div class="mb-3">
                                <label class="form-label">Обладнання, що замінюється</label>
                                <select name="replace_old_item_id" id="replaceOldItemId" class="form-select">
                                    <option value="">— Спершу оберіть філію і кабінет —</option>
                                </select>
                            </div>
                            <div class="mb-3" id="replaceActionWrap" style="display:none">
                                <label class="form-label">Куди діти старе обладнання</label>
                                <div class="d-flex gap-3 flex-wrap">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="replace_action" id="replaceActionRepair" value="repair">
                                        <label class="form-check-label" for="replaceActionRepair">В ремонт</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="replace_action" id="replaceActionTransfer" value="transfer">
                                        <label class="form-check-label" for="replaceActionTransfer">В інший кабінет</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="replace_action" id="replaceActionWarehouse" value="warehouse">
                                        <label class="form-check-label" for="replaceActionWarehouse">На склад</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-2 mb-2" id="replaceTransferFields" style="display:none">
                                <div class="col-md-6">
                                    <label class="form-label small">Філія призначення</label>
                                    <select name="replace_to_branch_id" class="form-select form-select-sm">
                                        <option value="">— Оберіть філію —</option>
                                        @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small">Номер кабінету призначення</label>
                                    <input type="text" name="replace_to_room_number" class="form-control form-control-sm">
                                </div>
                            </div>
                            <div class="mb-0">
                                <label class="form-label small">Примітка</label>
                                <input type="text" name="replace_note" class="form-control form-control-sm">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Скасувати</button>
                    <button type="submit" class="btn btn-info">
                        <i class="bi bi-box-arrow-right"></i> Видати
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
