<div x-data="alpineSelect()" x-ref="select-wrapper" x-init="initSelect()">
    {{-- Expect nothing but a select with options --}}
    {{ $slot }}

    <div class="input-group">
        <input class="form-input">
    </div>

</div>

@pushonce('js:alpineSelect')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('alpineSelect', () => ({
                open: false,
                disabled: false,
                value: null,
                options: null,


                initSelect() {
                    console.log(this.$refs['select-wrapper']);
                },

                toggle() {
                    this.open = ! this.open
                },
            }))
        })
    </script>
@endpushonce