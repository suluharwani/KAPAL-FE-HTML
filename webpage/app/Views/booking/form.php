<?= $this->extend('templates/default') ?>

<?= $this->section('content') ?>
<section class="booking">
    <div class="container">
        <h1 class="section-title">Pesan Kapal</h1>
        
        <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                <li><?= $error ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>
        
        <form action="<?= base_url('/booking/process') ?>" method="post">
            <?= csrf_field() ?>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="from">Dari Pulau</label>
                    <select id="from" name="from" class="form-control" required>
                        <option value="">Pilih Pulau Asal</option>
                        <?php foreach ($islands as $island): ?>
                        <option value="<?= strtolower($island) ?>" <?= old('from') === strtolower($island) ? 'selected' : '' ?>>
                            <?= $island ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="to">Ke Pulau</label>
                    <select id="to" name="to" class="form-control" required>
                        <option value="">Pilih Pulau Tujuan</option>
                        <?php foreach ($islands as $island): ?>
                        <option value="<?= strtolower($island) ?>" <?= old('to') === strtolower($island) ? 'selected' : '' ?>>
                            <?= $island ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="date">Tanggal Keberangkatan</label>
                    <input type="date" id="date" name="date" class="form-control" value="<?= old('date') ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="passengers">Jumlah Penumpang</label>
                    <input type="number" id="passengers" name="passengers" min="1" class="form-control" value="<?= old('passengers') ?>" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="boat_type">Tipe Kapal</label>
                <select id="boat_type" name="boat_type" class="form-control" required>
                    <option value="">Pilih Tipe Kapal</option>
                    <?php foreach ($boat_types as $type): ?>
                    <option value="<?= strtolower($type) ?>" <?= old('boat_type') === strtolower($type) ? 'selected' : '' ?>>
                        <?= $type ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-check">
                <input type="checkbox" id="round_trip" name="round_trip" class="form-check-input" <?= old('round_trip') ? 'checked' : '' ?>>
                <label for="round_trip" class="form-check-label">Pulang-Pergi</label>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block">Lanjutkan Pemesanan</button>
        </form>
    </div>
</section>
<?= $this->endSection() ?>