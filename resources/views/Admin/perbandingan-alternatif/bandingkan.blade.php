@extends('layouts.adminLayout')

@section('content')
<div class="col-md-12">
    <div class="card shadow">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="modal-title text-gray-900">
                Perbandingan Alternatif untuk Kriteria: {{ $kriteria->nama_kriteria }}
            </h5>
            <div>
                <a href="{{ route('perbandingan-alternatif.index.admin') }}?periode={{ request('periode') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-exchange-alt"></i> Ganti Kriteria
                </a>
            </div>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('perbandingan-alternatif.store.admin') }}">
                @csrf
                <input type="hidden" name="kriteria_id" value="{{ $kriteria->id }}">

                <div class="alert alert-info">
                    <strong>Petunjuk:</strong>
                    <ul class="mb-0">
                        <li>Skala 1-9 (1 = Sama penting, 9 = Sangat lebih penting)</li>
                        <li>Nilai pecahan (1/2, 1/3, dst.) akan otomatis terisi</li>
                    </ul>
                </div>

                <div class="row">
                    @php
                    $count = count($alternatif);
                    @endphp
                    @for ($i = 0; $i < $count; $i++)
                        @for ($j=$i + 1; $j < $count; $j++)
                        @php
                        $alt1=$alternatif[$i];
                        $alt2=$alternatif[$j];
                        @endphp
                        <div class="col-md-6 mb-3">
                        <label class="form-label text-gray-900"><strong>{{ $alt1->wilayah }}</strong> vs <strong>{{ $alt2->wilayah }}</strong></label>
                        <select name="nilai[{{ $alt1->id }}][{{ $alt2->id }}]" class="form-control">
                            <option value="1" selected>1 - Sama penting</option>
                            <option disabled>──────────────</option>
                            <optgroup label="{{ $alt1->wilayah }} lebih penting">
                                @for ($v = 2; $v <= 9; $v++)
                                    <option value="{{ $v }}">{{ $v }}</option>
                                    @endfor
                            </optgroup>
                            <optgroup label="{{ $alt2->wilayah }} lebih penting">
                                @for ($v = 2; $v <= 9; $v++)
                                    <option value="{{ 1 / $v }}">{{ $v }}</option>
                                    @endfor
                            </optgroup>
                        </select>
                </div>
                @endfor
                @endfor
        </div>

        <button type="submit" class="btn btn-primary mt-3">
            <i class="fas fa-save"></i> Simpan Perbandingan
        </button>
        </form>
    </div>
</div>
</div>
@endsection

@section('scripts')
<script>
    function updateReciprocal(selectElement, alt1Id, alt2Id) {
        const value = selectElement.value;
        const reciprocalValue = (1 / value).toFixed(4);

        // Update tampilan dan input reciprocal
        document.getElementById(`reciprocal-${alt2Id}-${alt1Id}`).textContent = `1/${value}`;
        document.getElementById(`input-reciprocal-${alt2Id}-${alt1Id}`).value = reciprocalValue;
    }
</script>
@endsection