@props([
    'name'      => 'files[]',
    'previewId' => 'preview_files',
    'fotoOnly'  => false,
    'required'  => false,
])

@php
    $accept     = $fotoOnly ? 'image/*' : 'image/*,.pdf,application/pdf';
    $acceptAttr = $fotoOnly ? '.jpg,.jpeg,.png' : '.jpg,.jpeg,.png,.pdf';
    $uid        = 'fu_' . str_replace(['[',']'], ['_',''], $name) . '_' . rand(1000,9999);
    $uidGallery = $uid . '_gallery';
    $uidCamera  = $uid . '_camera';
@endphp

<style>
.upload-zone-{{ $uid }} {
    border: 2px dashed #c7d0e8;
    border-radius: 10px;
    background: #f8f9ff;
    transition: border-color .2s, background .2s;
    cursor: pointer;
    text-align: center;
    padding: 10px 8px;
}
.upload-zone-{{ $uid }}:hover { border-color: #1a237e; background: #eef0fb; }
.preview-grid-{{ $uid }} {
    display: flex; flex-wrap: wrap; gap: 8px; margin-top: 8px;
}
.preview-item-{{ $uid }} {
    position: relative; width: 80px; height: 80px;
    border-radius: 8px; overflow: hidden; border: 2px solid #dee2e6;
    background: #f0f0f0;
}
.preview-item-{{ $uid }} img {
    width: 100%; height: 100%; object-fit: cover;
}
.preview-item-{{ $uid }} .pdf-thumb {
    display: flex; align-items: center; justify-content: center;
    width: 100%; height: 100%; flex-direction: column;
    font-size: 10px; color: #555; text-align: center; padding: 4px;
}
.preview-item-{{ $uid }} .remove-btn {
    position: absolute; top: 2px; right: 2px;
    background: rgba(200,0,0,0.75); color: #fff;
    border: none; border-radius: 50%; width: 20px; height: 20px;
    font-size: 11px; line-height: 1; cursor: pointer; display: flex;
    align-items: center; justify-content: center; padding: 0;
}
</style>

<input type="file" id="{{ $uidGallery }}" name="{{ $name }}"
    accept="{{ $acceptAttr }}" multiple
    style="display:none"
    {{ $required ? 'required' : '' }}>

<input type="file" id="{{ $uidCamera }}" name="{{ $name }}"
    accept="image/*" capture="environment" multiple
    style="display:none">

<div class="d-flex gap-2 mb-2">
    {{-- Tombol Upload dari galeri / komputer --}}
    <button type="button" class="btn btn-outline-secondary btn-sm flex-fill"
        onclick="document.getElementById('{{ $uidGallery }}').click()">
        <i class="bi bi-folder2-open me-1"></i>
        <span class="d-none d-sm-inline">Pilih File</span>
        <span class="d-inline d-sm-none">File</span>
    </button>

    {{-- Tombol Ambil Foto langsung dari kamera --}}
    <button type="button" class="btn btn-outline-primary btn-sm flex-fill"
        onclick="document.getElementById('{{ $uidCamera }}').click()">
        <i class="bi bi-camera me-1"></i>
        <span class="d-none d-sm-inline">Ambil Foto</span>
        <span class="d-inline d-sm-none">Kamera</span>
    </button>
</div>

<div class="upload-zone-{{ $uid }}" id="zone_{{ $uid }}"
    onclick="document.getElementById('{{ $uidGallery }}').click()"
    ondragover="event.preventDefault(); this.style.borderColor='#1a237e'"
    ondragleave="this.style.borderColor=''"
    ondrop="handleDrop_{{ $uid }}(event)">
    <i class="bi bi-cloud-upload text-muted" style="font-size:1.4rem;"></i>
    <div class="small text-muted mt-1">
        @if($fotoOnly)
            Seret foto ke sini atau klik untuk pilih
        @else
            Seret file ke sini atau klik untuk pilih
        @endif
    </div>
    <div style="font-size:0.7rem;" class="text-muted">
        @if($fotoOnly) JPG / PNG @else JPG / PNG / PDF @endif
        · Maks. 5MB · Bisa lebih dari 1
    </div>
</div>

<div class="preview-grid-{{ $uid }}" id="{{ $previewId }}"></div>

<script>
(function() {
    // Shared FileList accumulator (DataTransfer trick)
    let dt_{{ $uid }} = new DataTransfer();

    function renderPreviews() {
        const grid = document.getElementById('{{ $previewId }}');
        grid.innerHTML = '';
        const files = dt_{{ $uid }}.files;
        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            const wrap = document.createElement('div');
            wrap.className = 'preview-item-{{ $uid }}';

            if (file.type.startsWith('image/')) {
                const img = document.createElement('img');
                img.src = URL.createObjectURL(file);
                wrap.appendChild(img);
            } else {
                const div = document.createElement('div');
                div.className = 'pdf-thumb';
                div.innerHTML = '<i class="bi bi-file-earmark-pdf text-danger" style="font-size:1.5rem;"></i><span>' +
                    file.name.substring(0, 12) + (file.name.length > 12 ? '…' : '') + '</span>';
                wrap.appendChild(div);
            }

            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'remove-btn';
            btn.innerHTML = '×';
            btn.title = 'Hapus';
            const idx = i;
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                const newDt = new DataTransfer();
                const arr = Array.from(dt_{{ $uid }}.files);
                arr.splice(idx, 1);
                arr.forEach(f => newDt.items.add(f));
                dt_{{ $uid }} = newDt;
                syncInputs();
                renderPreviews();
            });
            wrap.appendChild(btn);
            grid.appendChild(wrap);
        }
        // Update zone hint
        const zone = document.getElementById('zone_{{ $uid }}');
        zone.querySelector('div').style.display = files.length > 0 ? 'none' : '';
    }

    function addFiles(fileList) {
        for (const file of fileList) {
            // Cek duplikat berdasarkan nama + ukuran
            let dup = false;
            for (const existing of dt_{{ $uid }}.files) {
                if (existing.name === file.name && existing.size === file.size) { dup = true; break; }
            }
            if (!dup) dt_{{ $uid }}.items.add(file);
        }
        syncInputs();
        renderPreviews();
    }

    function syncInputs() {
        // Sync ke kedua input agar ikut ter-submit
        const gal = document.getElementById('{{ $uidGallery }}');
        const cam = document.getElementById('{{ $uidCamera }}');
        try { gal.files = dt_{{ $uid }}.files; } catch(e) {}
        // required check
        if ({{ $required ? 'true' : 'false' }}) {
            gal.required = dt_{{ $uid }}.files.length === 0;
        }
    }

    document.getElementById('{{ $uidGallery }}').addEventListener('change', function() {
        addFiles(this.files);
        this.value = ''; // reset so same file can be re-added if needed
    });

    document.getElementById('{{ $uidCamera }}').addEventListener('change', function() {
        addFiles(this.files);
        this.value = '';
    });

    window['handleDrop_{{ $uid }}'] = function(event) {
        event.preventDefault();
        document.getElementById('zone_{{ $uid }}').style.borderColor = '';
        addFiles(event.dataTransfer.files);
    };
})();
</script>
