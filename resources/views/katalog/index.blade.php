@extends('layouts.app')

@section('title', 'Katalog Produk - RZ Store')

@section('content')
    {{-- Page Header --}}
    <div class="page-header">
        <h1 class="page-title">Katalog Produk</h1>
        <p class="page-subtitle">Pilih paket kuota internet atau layanan akun VPN server pribadi</p>
    </div>

    {{-- Search Bar --}}
    <div class="search-bar">
        <div class="search-input-wrapper">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            <input type="text" class="search-input" placeholder="Cari paket internet, VPN, atau provider..." id="searchProduct">
        </div>
    </div>

    {{-- Category Tabs --}}
    @php
        $uniqueCategories = $products->pluck('category')->unique()->values();
    @endphp

    <div class="category-tabs" id="categoryTabs">
        <button class="category-tab active" data-category="all" onclick="filterCategory('all', this)">
            🛒 Semua ({{ $products->count() }})
        </button>
        @foreach($uniqueCategories as $cat)
        <button class="category-tab" data-category="{{ $cat }}" onclick="filterCategory('{{ $cat }}', this)">
            @if(str_contains(strtolower($cat), 'vpn'))
                🔒 {{ $cat }}
            @elseif(str_contains(strtolower($cat), 'telkomsel'))
                🔴 {{ $cat }}
            @elseif(str_contains(strtolower($cat), 'indosat'))
                🟡 {{ $cat }}
            @elseif(str_contains(strtolower($cat), 'xl') || str_contains(strtolower($cat), 'axis'))
                🔵 {{ $cat }}
            @else
                📶 {{ $cat }}
            @endif
        </button>
        @endforeach
    </div>

    {{-- Real Database Products Grid --}}
    <div class="category-section" id="productGridContainer">
        @if($products->count() > 0)
            <div class="product-grid" id="productGrid">
                @foreach($products as $prod)
                <div class="product-card" data-category="{{ $prod->category }}" data-search="{{ strtolower($prod->name . ' ' . $prod->sku . ' ' . $prod->category . ' ' . $prod->description) }}">
                    @if($prod->category === 'VPN Premium')
                        <span class="product-badge-vpn">VPS SG</span>
                    @else
                        <span class="product-badge-popular">{{ $prod->category }}</span>
                    @endif

                    <div class="product-card-header">
                        <div class="product-provider">
                            <div class="provider-icon {{ $prod->category === 'VPN Premium' ? 'vpn' : strtolower($prod->category) }}" style="font-weight:800;">
                                {{ substr($prod->category, 0, 2) }}
                            </div>
                            <span class="provider-name">{{ $prod->category }}</span>
                        </div>
                    </div>

                    <div class="product-name">{{ $prod->name }}</div>
                    <div class="product-description">{{ $prod->description ?: 'Paket kuota internet resmi cepat dan stabil.' }} • <strong>{{ $prod->active_period }}</strong></div>

                    <div class="product-footer">
                        <div class="product-price">Rp {{ number_format($prod->sell_price, 0, ',', '.') }}</div>
                        <button class="btn-buy" onclick="orderProduct('{{ addslashes($prod->name) }}', {{ $prod->sell_price }}, {{ $prod->id }}, '{{ $prod->category }}', '{{ $prod->active_period }}')">
                            Beli Sekarang
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div style="background:#FFFFFF;border-radius:16px;padding:48px 24px;text-align:center;border:1px solid #E2E8F0;margin-top:20px;">
                <div style="font-size:42px;margin-bottom:12px;">📦</div>
                <h3 style="font-size:18px;font-weight:700;color:#0F172A;margin-bottom:6px;">Belum Ada Produk di Katalog</h3>
                <p style="font-size:14px;color:#64748B;max-width:440px;margin:0 auto 20px;">
                    Produk dapat ditambahkan, diubah, atau disinkronkan melalui Panel Admin.
                </p>
                <a href="{{ route('admin.produk') }}" class="btn btn-primary" style="display:inline-flex;align-items:center;gap:8px;padding:10px 20px;border-radius:10px;background:#4361EE;color:#FFFFFF;">
                    + Tambah Produk di Panel Admin
                </a>
            </div>
        @endif
    </div>

    {{-- Order Modal (Professional Design) --}}
    <div class="modal-overlay" id="orderModal" style="display: none; position: fixed; inset: 0; z-index: 9999; background: rgba(15, 23, 42, 0.7); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); align-items: center; justify-content: center; padding: 20px;">
        <div class="modal-content" style="background: #FFFFFF; border-radius: 20px; width: 100%; max-width: 520px; box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.4), 0 0 0 1px rgba(226, 232, 240, 0.8); overflow: hidden; animation: modalAppear 0.25s cubic-bezier(0.16, 1, 0.3, 1) forwards;">
            {{-- Header --}}
            <div style="padding: 20px 24px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #F1F5F9; background: #FFFFFF;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 40px; height: 40px; border-radius: 12px; background: #EEF2FF; color: #4361EE; display: flex; align-items: center; justify-content: center;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/>
                            <line x1="3" y1="6" x2="21" y2="6"/>
                            <path d="M16 10a4 4 0 01-8 0"/>
                        </svg>
                    </div>
                    <div>
                        <h3 style="font-size: 17px; font-weight: 800; color: #0F172A; margin: 0; letter-spacing: -0.01em;">Form Pemesanan</h3>
                        <p style="font-size: 12px; color: #64748B; margin: 2px 0 0 0;">Konfirmasi detail pembelian paket</p>
                    </div>
                </div>
                <button onclick="closeOrderModal()" style="width: 32px; height: 32px; border-radius: 8px; background: #F1F5F9; border: none; color: #64748B; font-size: 20px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s;">&times;</button>
            </div>

            {{-- Body --}}
            <div style="padding: 24px; background: #FFFFFF;">
                {{-- Product Detail Card --}}
                <div style="background: linear-gradient(135deg, #F8FAFC, #EEF2FF); border-radius: 14px; padding: 18px 20px; margin-bottom: 20px; border: 1px solid #E0E7FF; display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <div style="font-size: 11px; color: #6366F1; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">Produk Dipilih</div>
                        <div id="modalProductName" style="font-weight: 800; font-size: 16px; color: #0F172A;">-</div>
                        <div style="font-size: 12px; color: #64748B; margin-top: 4px; display: flex; align-items: center; gap: 4px;">
                            <span>⏱️ Masa Aktif: <strong id="modalPeriodText" style="color: #334155;">30 Hari</strong></span>
                        </div>
                    </div>
                    <div style="text-align: right;">
                        <div style="font-size: 11px; color: #64748B; font-weight: 500;">Total Harga</div>
                        <div id="modalProductPrice" style="font-weight: 900; font-size: 20px; color: #4361EE; margin-top: 2px;">Rp 0</div>
                    </div>
                </div>

                @guest
                    <div style="background: #FFFBEB; border: 1px solid #FDE68A; border-radius: 12px; padding: 12px 16px; margin-bottom: 18px; font-size: 12px; color: #92400E; display: flex; align-items: center; justify-content: space-between; gap: 8px;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <span>🔒</span>
                            <span><strong>Perhatian:</strong> Anda harus masuk (login) untuk membuat pesanan.</span>
                        </div>
                        <a href="{{ route('login') }}" style="color: #4361EE; font-weight: 700; text-decoration: none; background: #EEF2FF; padding: 4px 10px; border-radius: 6px;">Masuk</a>
                    </div>
                @endguest

                {{-- Target Number Form Group --}}
                <div style="margin-bottom: 18px;">
                    <label style="display: block; font-size: 13px; font-weight: 700; color: #1E293B; margin-bottom: 8px;" id="targetInputLabel">
                        Nomor HP / Akun Tujuan
                    </label>
                    <div style="position: relative;">
                        <input type="text" id="targetNumber" placeholder="Contoh: 081234567890" style="width: 100%; padding: 12px 14px 12px 42px; border-radius: 12px; border: 1.5px solid #CBD5E1; font-size: 14px; font-weight: 600; color: #0F172A; background: #F8FAFC; outline: none; transition: all 0.2s;">
                        <svg style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #64748B;" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="5" y="2" width="14" height="20" rx="2" ry="2"/>
                            <line x1="12" y1="18" x2="12.01" y2="18"/>
                        </svg>
                    </div>
                    <div style="font-size: 12px; color: #64748B; margin-top: 6px; display: flex; align-items: center; gap: 4px;" id="targetInputHint">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                        <span>Pastikan nomor tujuan sudah benar untuk proses pengisian otomatis.</span>
                    </div>
                </div>

                {{-- Notes (Optional) --}}
                <div style="margin-bottom: 8px;">
                    <label style="display: block; font-size: 13px; font-weight: 700; color: #1E293B; margin-bottom: 8px;">
                        Catatan Pesanan <span style="font-size: 12px; font-weight: 400; color: #94A3B8;">(Opsional)</span>
                    </label>
                    <div style="position: relative;">
                        <textarea id="orderNotes" rows="2" placeholder="Catatan tambahan bila diperlukan..." style="width: 100%; padding: 10px 14px 10px 42px; border-radius: 12px; border: 1.5px solid #CBD5E1; font-size: 13px; color: #0F172A; background: #F8FAFC; outline: none; resize: none;"></textarea>
                        <svg style="position: absolute; left: 14px; top: 14px; color: #64748B;" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div style="padding: 16px 24px; background: #FAFBFD; border-top: 1px solid #F1F5F9; display: flex; align-items: center; justify-content: space-between; gap: 12px;">
                <button type="button" onclick="closeOrderModal()" style="padding: 10px 20px; border-radius: 10px; border: 1px solid #E2E8F0; background: #FFFFFF; font-size: 13px; font-weight: 600; color: #64748B; cursor: pointer;">
                    Batal
                </button>
                <button type="button" onclick="proceedToCheckout()" style="padding: 12px 24px; border-radius: 12px; border: none; background: #4361EE; color: #FFFFFF; font-size: 14px; font-weight: 700; cursor: pointer; box-shadow: 0 4px 14px rgba(67, 97, 238, 0.3); display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s;">
                    <span>Lanjut ke Pembayaran QRIS</span>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </button>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    let selectedProduct = '';
    let selectedPrice = 0;
    let selectedProductId = null;
    let selectedCategory = '';
    let selectedPeriod = '';

    // Category filter
    function filterCategory(category, btn) {
        document.querySelectorAll('.category-tab').forEach(t => t.classList.remove('active'));
        btn.classList.add('active');

        const cards = document.querySelectorAll('.product-card');
        cards.forEach(c => {
            if (category === 'all' || c.dataset.category === category) {
                c.style.display = 'flex';
            } else {
                c.style.display = 'none';
            }
        });
    }

    // Search filter
    document.getElementById('searchProduct')?.addEventListener('input', function(e) {
        const query = e.target.value.toLowerCase();
        const cards = document.querySelectorAll('.product-card');

        if (!query) {
            const activeTab = document.querySelector('.category-tab.active');
            filterCategory(activeTab?.dataset.category || 'all', activeTab);
            return;
        }

        cards.forEach(card => {
            const searchData = card.dataset.search || '';
            const match = searchData.includes(query);
            card.style.display = match ? 'flex' : 'none';
        });
    });

    // Order modal
    function orderProduct(productName, price, productId = null, category = '', activePeriod = '30 Hari') {
        selectedProduct = productName;
        selectedPrice = price;
        selectedProductId = productId;
        selectedCategory = category;
        selectedPeriod = activePeriod;

        document.getElementById('modalProductName').textContent = productName;
        document.getElementById('modalPeriodText').textContent = activePeriod || '30 Hari';
        document.getElementById('modalProductPrice').textContent = 'Rp ' + price.toLocaleString('id-ID');
        document.getElementById('targetNumber').value = '';
        document.getElementById('orderNotes').value = '';

        if (category === 'VPN Premium') {
            document.getElementById('targetInputLabel').textContent = 'Username Akun VPN / No. WhatsApp';
            document.getElementById('targetNumber').placeholder = 'Contoh: rz_vpn_user atau 08123456789';
            document.getElementById('targetInputHint').innerHTML = `
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                <span>Kredensial VPN (WireGuard / V2Ray) akan dikirimkan ke kontak ini.</span>
            `;
        } else {
            document.getElementById('targetInputLabel').textContent = 'Nomor WhatsApp / HP Tujuan';
            document.getElementById('targetNumber').placeholder = 'Contoh: 081234567890';
            document.getElementById('targetInputHint').innerHTML = `
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                <span>Pastikan nomor tujuan sudah benar untuk pengisian kuota otomatis dari OkeConnect.</span>
            `;
        }

        const modal = document.getElementById('orderModal');
        modal.style.display = 'flex';
        modal.classList.add('active');
        document.getElementById('targetNumber').focus();
    }

    function closeOrderModal() {
        const modal = document.getElementById('orderModal');
        modal.style.display = 'none';
        modal.classList.remove('active');
    }

    function proceedToCheckout() {
        const targetNumber = document.getElementById('targetNumber').value.trim();
        if (!targetNumber) {
            alert('Silakan masukkan nomor tujuan / akun');
            document.getElementById('targetNumber').focus();
            return;
        }

        const queryParams = {
            product: selectedProduct,
            price: selectedPrice,
            target: targetNumber,
            notes: document.getElementById('orderNotes').value.trim()
        };
        if (selectedProductId) {
            queryParams.product_id = selectedProductId;
        }

        const params = new URLSearchParams(queryParams);
        window.location.href = `/checkout?${params.toString()}`;
    }

    // Close modal on outside click
    document.getElementById('orderModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeOrderModal();
    });
</script>
@endsection
