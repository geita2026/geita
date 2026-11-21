<!-- ===================== STYLES ===================== -->
<style>
    .companyReg {
        transition: background-color .25s ease;
    }

    .companyReg:hover {
        background-color: rgba(47, 214, 214, 0.08);
    }

    /* Smooth animation */
    .collapse-animate {
        overflow: hidden;
        max-height: 0;
        opacity: 0;
        transition: max-height 0.35s ease, opacity 0.25s ease;
        pointer-events: none;
    }

    .collapse-animate.show {
        max-height: 500px;
        opacity: 1;
        pointer-events: auto;
    }

    /* Progressive enhancement */
    .rotate-icon {
        transition: transform .3s ease;
    }

    .rotate-icon.open {
        transform: rotate(45deg);
    }

    .choice-hint {
        background: rgba(47, 214, 214, 0.05);
        border-left: 3px solid rgba(47, 214, 214, 0.6);
        padding: 8px 12px;
        margin-top: 10px;
        font-size: .85rem;
        color: #495057;
    }
</style>

<!-- ===================== UI ===================== -->
<div class="row">
    <div class="col-lg-12">

        <div class="card border-info">
            <div class="card-body p-0">

                <!-- Header -->
                <div class="companyReg">
                    <div class="media-link cursor-pointer p-4"
                         data-toggle="collapse"
                         data-target="#newCompany"
                         style="border-bottom:1px solid rgba(47,214,214,.3)">
                        <h5 class="mb-0 d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <i class="icon-plus-circle2 text-primary mr-3 rotate-icon"
                                   id="toggleIcon" style="font-size:22px"></i>
                                <div>
                                    <div class="text-bold text-primary">
                                        You haven’t registered your Company or Institution yet
                                    </div>
                                    <small class="text-muted">
                                        Click to begin registration using the appropriate option below
                                    </small>
                                </div>
                            </div>
                            <i class="icon-arrow-right13 text-primary"></i>
                        </h5>
                    </div>
                </div>

                <!-- Collapsible Content -->
                <div class="collapse" id="newCompany">
                    <div class="p-4">

                        <!-- Originality -->
                        <div class="row form-group">
                            <div class="col-md-12">
                                <label>
                                    Company or Institution Registration Category
                                    <i class="text-danger">*</i>
                                </label>

                                <small class="text-muted d-block mb-2">
                                    Select how your company or institution is legally registered.
                                    This determines the registration process and required documentation.
                                </small>

                                <select class="form-control" id="originality">
                                    <option value="">Select registration category</option>
                                    <option value="1">
                                        Registered in Tanzania (Local Company / Institution)
                                    </option>
                                    <option value="0">
                                        Registered Outside Tanzania (Foreign Company / Institution)
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Foreign Company -->
                        <div class="row form-group collapse-animate" id="foreignCompany">
                            <div class="col-lg-12 text-center">
                                <a class="btn btn-outline-info btn-rounded text-bold"
                                   href="{{ route('companies.create') }}">
                                    Register Foreign Company
                                    <i class="icon-arrow-right13 ml-1"></i>
                                </a>

                                <div class="choice-hint collapse-animate" id="foreignHint">
                                    Foreign companies must complete full registration before
                                    participating in exhibitions.
                                </div>
                            </div>
                        </div>

                        <!-- Local Company -->
                        <div class="row form-group collapse-animate" id="localCompany">
                            <div class="col-lg-6">
                                <div class="form-check">
                                    <input type="checkbox" id="TSRN" class="form-check-input control-primary">
                                    <label class="form-check-label" for="TSRN">
                                        I already have a Service Registration Number (SRN)
                                    </label>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-check">
                                    <input type="checkbox" id="TIN" class="form-check-input control-primary" name="TIN">
                                    <label class="form-check-label" for="TIN">
                                        I want to register using my Tax Identification Number (TIN)
                                    </label>
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="choice-hint collapse-animate" id="localHint">
                                    Choose <strong>SRN</strong> if you have exhibited with us before.
                                    Choose <strong>TIN</strong> if this is your first registration.
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- TSRN FIELD -->
                <div class="p-4">
                    <div class="row align-items-center collapse-animate" id="TSRNFIELD">
                        <div class="col-md-10">
                            <form action="{{ route('company_profile', ['companyId' => date('dmd')]) }}"
                                  method="post" id="registerWithTsrn">
                                @csrf
                                @method('GET')
                                <input type="text"
                                       name="TSRN"
                                       class="form-control"
                                       placeholder="Enter Service Registration Number">
                            </form>
                        </div>
                        <div class="col-md-2 text-center">
                            <button class="btn btn-primary btn-rounded"
                                    onclick="event.preventDefault();document.getElementById('registerWithTsrn').submit();">
                                Continue
                                <i class="icon-arrow-right13"></i>
                            </button>
                        </div>
                    </div>

                    <!-- TIN FIELD -->
                    <div class="row align-items-center collapse-animate" id="tinField">
                        <div class="col-md-10">
                            <form action="{{ route('company_profile', ['companyId' => date('dmd')]) }}"
                                  method="post" id="registerWithTin">
                                @csrf
                                @method('GET')
                                <input type="text"
                                       name="TIN"
                                       id="numberInput"
                                       maxlength="9"
                                       class="form-control"
                                       placeholder="Enter Tax Identification Number">
                            </form>
                        </div>
                        <div class="col-md-2 text-center">
                            <button class="btn btn-primary btn-rounded"
                                    onclick="event.preventDefault();document.getElementById('registerWithTin').submit();">
                                Continue
                                <i class="icon-arrow-right13"></i>
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>

<!-- ===================== SCRIPTS ===================== -->
<script>
    const checkboxTSRN = document.getElementById('TSRN');
    const checkboxTIN  = document.getElementById('TIN');

    const TSRNDiv = document.getElementById('TSRNFIELD');
    const TINDiv  = document.getElementById('tinField');

    const foreignCompany = document.getElementById('foreignCompany');
    const localCompany   = document.getElementById('localCompany');

    const localHint  = document.getElementById('localHint');
    const foreignHint = document.getElementById('foreignHint');
    const toggleIcon = document.getElementById('toggleIcon');

    function show(el) { el.classList.add('show'); }
    function hide(el) { el.classList.remove('show'); }

    // Collapse icon rotation
    $('#newCompany').on('shown.bs.collapse', () => toggleIcon.classList.add('open'));
    $('#newCompany').on('hidden.bs.collapse', () => toggleIcon.classList.remove('open'));

    // Mutually exclusive TSRN / TIN
    checkboxTSRN.addEventListener('change', function () {
        if (this.checked) {
            checkboxTIN.checked = false;
            show(TSRNDiv);
            hide(TINDiv);
        } else {
            hide(TSRNDiv);
        }
    });

    checkboxTIN.addEventListener('change', function () {
        if (this.checked) {
            checkboxTSRN.checked = false;
            show(TINDiv);
            hide(TSRNDiv);
        } else {
            hide(TINDiv);
        }
    });

    // Originality logic
    $("#originality").change(function () {

        checkboxTSRN.checked = false;
        checkboxTIN.checked  = false;

        hide(TSRNDiv);
        hide(TINDiv);
        hide(localHint);
        hide(foreignHint);

        if ($(this).val() === "1") {
            show(localCompany);
            hide(foreignCompany);
            show(localHint);
        }

        if ($(this).val() === "0") {
            show(foreignCompany);
            hide(localCompany);
            show(foreignHint);
        }
    });

    // Numeric-only TIN
    document.getElementById('numberInput').addEventListener('keypress', function (e) {
        const char = String.fromCharCode(e.which);
        if (!/^[0-9]+$/.test(char)) e.preventDefault();
    });
</script>
