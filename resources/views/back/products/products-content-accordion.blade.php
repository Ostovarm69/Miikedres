<style>
    .card .card-header {
        padding: 1.5rem;
    }
</style>
<div id="accordion">
    @foreach($filesArray as $directory => $files)
        <div class="card mb-0">
            <div class="card-header" id="heading-{{ $loop->index }}">
                <h5 class="mb-0">
                    <button class="btn btn-link" data-toggle="collapse" data-target="#collapse-{{ $loop->index }}" aria-expanded="true" aria-controls="collapse{{ $loop->index }}">
                        {{ $directory }}
                    </button>
                </h5>
            </div>

            <div id="collapse-{{ $loop->index }}" class="collapse @if($loop->index === 0) show @endif" aria-labelledby="heading-{{ $loop->index }}" data-parent="#accordion">
                <div class="card-body">
                    @foreach($files as $file)
                        @if($file['type'] === 'image')
                            <a href="{{ route('admin.product.download.content') }}?file_path=/uploads/{{ $file['file'] }}" target="_blank">
                                <img style="max-height: 200px;"
                                     src="/uploads/{{ $file['file'] }}"
                                     class="img-fluid img-thumbnail"
                                >
                            </a>
                        @else
                            <div style="padding: 10px; float: right;">
                                <a style="float: right;" href="/uploads/{{ $file['file'] }}" target="_blank" class="btn btn-secondary">
                                    دانلود ویدئو
                                </a>
                                <video style="max-width: 100px" controls>
                                    <source src="/uploads/{{ $file['file'] }}">
                                </video>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
        @endforeach
        <div class="card mb-0">
            <div class="card-header" id="heading-caption">
                <h5 class="mb-0">
                    <button class="btn btn-link" data-toggle="collapse" data-target="#collapse-caption" aria-expanded="true" aria-controls="collapsecaption">
                        کپشن
                    </button>
                </h5>
            </div>

            <div id="collapse-caption" class="collapse" aria-labelledby="heading-caption" data-parent="#accordion">
                <div class="card-body">
                    <pre style="cursor: pointer;" onclick="copyContent()" id="copy" class="p-1">{{ $caption }}</pre>
                </div>
            </div>
        </div>
</div>

<script>
    let text = document.getElementById('copy').innerHTML;
    const copyContent = async () => {
        try {
            await navigator.clipboard.writeText(text);
            alert('متن کپشن کپی شد.');
        } catch (err) {
            console.error('Failed to copy: ', err);
        }
    }
</script>
