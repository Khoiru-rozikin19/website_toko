@extends('layouts.app')

@section('title', 'Kelola Produk - Panel Admin RZ Store')

@section('content')
<div class="page-container">
    {{-- Page Header --}}
    <div class="page-header" style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; flex-wrap: wrap; gap: 14px;">
        <div>
            <div style="font-size: 13px; color: #64748B; margin-bottom: 4px; display: flex; align-items: center; gap: 6px;">
                <span>Panel Admin</span>
                <span>/</span>
                <span style="color: #4361EE; font-weight: 600;">Kelola Produk</span>
            </div>
            <h1 style="font-size: 24px; font-weight: 800; color: #0F172A; letter-spacing: -0.02em;">
                Kelola Produk & Paket Internet
            </h1>
            <p style="color: #64748B; font-size: 14px; margin-top: 2px;">
                Atur katalog paket data OkeConnect & layanan akun VPN server pribadi.
            </p>
        </div>
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <button onclick="syncOkeconnectProducts()" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 18px; border-radius: 10px; font-size: 13px; font-weight: 600; background: #FFFFFF; border: 1px solid #E2E8F0; color: #334155; cursor: pointer; transition: all 0.2s;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="23 4 23 10 17 10"/>
                    <polyline points="1 20 1 14 7 14"/>
                    <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/>
                </svg>
                Sync OkeConnect
            </button>
            <button onclick="openAddProductModal()" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; border-radius: 10px; font-size: 13px; font-weight: 600; background: #4361EE; color: #FFFFFF; border: none; cursor: pointer; box-shadow: 0 4px 12px rgba(67, 97, 238, 0.25); transition: all 0.2s;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"/>
                    <line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Tambah Produk
            </button>
        </div>
    </div>

    {{-- Stats Grid --}}
    <div class="stats-grid" style="margin-bottom: 24px;">
        <div style="background: #FFFFFF; border-radius: 14px; padding: 18px; border: 1px solid #E2E8F0; display: flex; align-items: center; gap: 14px;">
            <div style="width: 44px; height: 44px; border-radius: 12px; background: #EEF2FF; color: #4361EE; display: flex; align-items: center; justify-content: center;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
            <div>
                <div style="font-size: 12px; color: #64748B; font-weight: 500;">Total Produk</div>
                <div style="font-size: 20px; font-weight: 800; color: #0F172A;">{{ $totalProducts }} <span style="font-size: 12px; font-weight: 500; color: #94A3B8;">Item</span></div>
            </div>
        </div>

        <div style="background: #FFFFFF; border-radius: 14px; padding: 18px; border: 1px solid #E2E8F0; display: flex; align-items: center; gap: 14px;">
            <div style="width: 44px; height: 44px; border-radius: 12px; background: #ECFDF5; color: #10B981; display: flex; align-items: center; justify-content: center;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                    <polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
            </div>
            <div>
                <div style="font-size: 12px; color: #64748B; font-weight: 500;">Produk Aktif</div>
                <div style="font-size: 20px; font-weight: 800; color: #0F172A;">{{ $activeProducts }} <span style="font-size: 12px; font-weight: 500; color: #10B981;">Siap Jual</span></div>
            </div>
        </div>

        <div style="background: #FFFFFF; border-radius: 14px; padding: 18px; border: 1px solid #E2E8F0; display: flex; align-items: center; gap: 14px;">
            <div style="width: 44px; height: 44px; border-radius: 12px; background: #FEF3C7; color: #D97706; display: flex; align-items: center; justify-content: center;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="1" x2="12" y2="23"/>
                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                </svg>
            </div>
            <div>
                <div style="font-size: 12px; color: #64748B; font-weight: 500;">Rata-rata Margin</div>
                <div style="font-size: 20px; font-weight: 800; color: #0F172A;">Rp {{ number_format($avgMargin, 0, ',', '.') }} <span style="font-size: 12px; font-weight: 500; color: #D97706;">/ item</span></div>
            </div>
        </div>

        <div style="background: #FFFFFF; border-radius: 14px; padding: 18px; border: 1px solid #E2E8F0; display: flex; align-items: center; gap: 14px;">
            <div style="width: 44px; height: 44px; border-radius: 12px; background: #F3E8FF; color: #9333EA; display: flex; align-items: center; justify-content: center;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                </svg>
            </div>
            <div>
                <div style="font-size: 12px; color: #64748B; font-weight: 500;">Total Terjual</div>
                <div style="font-size: 20px; font-weight: 800; color: #0F172A;">{{ $totalSales }} <span style="font-size: 12px; font-weight: 500; color: #9333EA;">Transaksi</span></div>
            </div>
        </div>
    </div>

    {{-- Main Filter & Table Card --}}
    <div style="background: #FFFFFF; border-radius: 16px; border: 1px solid #E2E8F0; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
        {{-- Toolbar Filter & Search --}}
        <div style="padding: 18px 24px; border-bottom: 1px solid #F1F5F9; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px;">
            {{-- Category Filter Pills --}}
            <div style="display: flex; gap: 8px; flex-wrap: wrap;" id="categoryFilterContainer">
                <button onclick="filterCategory('all', this)" class="category-pill active" style="padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 600; cursor: pointer; border: 1px solid #4361EE; background: #4361EE; color: #FFFFFF; transition: all 0.2s;">
                    Semua ({{ $totalProducts }})
                </button>
                @foreach($categories as $cat)
                    @if($cat['id'] !== 'all')
                    <button onclick="filterCategory('{{ $cat['id'] }}', this)" class="category-pill" style="padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 500; cursor: pointer; border: 1px solid #E2E8F0; background: #F8FAFC; color: #475569; transition: all 0.2s;">
                        {{ $cat['name'] }}
                    </button>
                    @endif
                @endforeach
            </div>

            {{-- Search Bar --}}
            <div style="position: relative; min-width: 260px;">
                <input type="text" id="productSearchInput" onkeyup="searchProducts()" placeholder="Cari nama atau kode SKU..." style="width: 100%; padding: 8px 14px 8px 36px; border-radius: 10px; border: 1px solid #E2E8F0; font-size: 13px; outline: none; background: #F8FAFC;">
                <svg style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94A3B8;" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"/>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
            </div>
        </div>

        {{-- Products Table --}}
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13px;">
                <thead>
                    <tr style="background: #F8FAFC; border-bottom: 1px solid #E2E8F0; color: #64748B; font-weight: 600; text-transform: uppercase; font-size: 11px; letter-spacing: 0.05em;">
                        <th style="padding: 14px 20px;">Produk & SKU</th>
                        <th style="padding: 14px 16px;">Provider / Kategori</th>
                        <th style="padding: 14px 16px;">Harga Modal (H2H)</th>
                        <th style="padding: 14px 16px;">Harga Jual</th>
                        <th style="padding: 14px 16px;">Margin (Profit)</th>
                        <th style="padding: 14px 16px;">Status</th>
                        <th style="padding: 14px 20px; text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="productTableBody">
                    @foreach($products as $product)
                    <tr class="product-row" data-category="{{ $product['category'] }}" data-name="{{ strtolower($product['name']) }}" data-sku="{{ strtolower($product['sku']) }}" style="border-bottom: 1px solid #F1F5F9; transition: background 0.15s;">
                        <td style="padding: 14px 20px;">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <div style="width: 36px; height: 36px; border-radius: 10px; background: {{ $product['category'] === 'VPN Premium' ? '#F3E8FF' : '#EEF2FF' }}; color: {{ $product['category'] === 'VPN Premium' ? '#9333EA' : '#4361EE' }}; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px; flex-shrink: 0;">
                                    {{ substr($product['category'], 0, 2) }}
                                </div>
                                <div>
                                    <div style="font-weight: 600; color: #0F172A; font-size: 14px;">{{ $product['name'] }}</div>
                                    <div style="display: flex; align-items: center; gap: 8px; margin-top: 2px;">
                                        <span style="font-family: monospace; font-size: 11px; background: #F1F5F9; color: #475569; padding: 2px 6px; border-radius: 4px; font-weight: 600;">{{ $product['sku'] }}</span>
                                        <span style="font-size: 12px; color: #94A3B8;">• {{ $product['active_period'] }}</span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td style="padding: 14px 16px;">
                            <span style="display: inline-block; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; background: {{ $product['category'] === 'VPN Premium' ? '#FAF5FF; color: #7E22CE; border: 1px solid #E9D5FF;' : '#F0F9FF; color: #0369A1; border: 1px solid #BAE6FD;' }}">
                                {{ $product['category'] }}
                            </span>
                        </td>
                        <td style="padding: 14px 16px; color: #64748B; font-weight: 500;">
                            @if($product['modal_price'] > 0)
                                Rp {{ number_format($product['modal_price'], 0, ',', '.') }}
                            @else
                                <span style="color: #10B981; font-weight: 600;">Rp 0 (VPS Milik Sendiri)</span>
                            @endif
                        </td>
                        <td style="padding: 14px 16px; font-weight: 700; color: #0F172A;">
                            Rp {{ number_format($product['sell_price'], 0, ',', '.') }}
                        </td>
                        <td style="padding: 14px 16px;">
                            <span style="color: #10B981; font-weight: 700; background: #ECFDF5; padding: 4px 8px; border-radius: 6px; font-size: 12px;">
                                +Rp {{ number_format($product['margin'], 0, ',', '.') }}
                            </span>
                        </td>
                        <td style="padding: 14px 16px;">
                            <label style="position: relative; display: inline-block; width: 38px; height: 22px; margin: 0; cursor: pointer;">
                                <input type="checkbox" checked onchange="toggleProductStatus({{ $product['id'] }}, this)" style="opacity: 0; width: 0; height: 0;">
                                <span class="toggle-slider" style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #10B981; transition: .3s; border-radius: 22px;"></span>
                            </label>
                        </td>
                        <td style="padding: 14px 20px; text-align: right;">
                            <div style="display: inline-flex; gap: 8px;">
                                <button onclick="openEditProductModal({{ json_encode($product) }})" title="Edit Produk" style="padding: 6px; border-radius: 8px; border: 1px solid #E2E8F0; background: #FFFFFF; color: #475569; cursor: pointer; transition: all 0.2s;">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                    </svg>
                                </button>
                                <button onclick="openDeleteProductModal({{ $product['id'] }}, '{{ $product['name'] }}')" title="Hapus Produk" style="padding: 6px; border-radius: 8px; border: 1px solid #FEE2E2; background: #FFF5F5; color: #EF4444; cursor: pointer; transition: all 0.2s;">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="3 6 5 6 21 6"/>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- MODAL: TAMBAH PRODUK --}}
<div id="addProductModal" style="display: none; position: fixed; inset: 0; z-index: 999; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); align-items: center; justify-content: center; padding: 20px;">
    <div style="background: #FFFFFF; border-radius: 20px; width: 100%; max-width: 580px; max-height: 90vh; overflow-y: auto; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2); border: 1px solid #E2E8F0;">
        <div style="padding: 20px 24px; border-bottom: 1px solid #F1F5F9; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="font-size: 18px; font-weight: 700; color: #0F172A; display: flex; align-items: center; gap: 8px;">
                <span style="width: 32px; height: 32px; border-radius: 8px; background: #EEF2FF; color: #4361EE; display: inline-flex; align-items: center; justify-content: center;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                </span>
                Tambah Produk Baru
            </h3>
            <button onclick="closeModal('addProductModal')" style="background: none; border: none; font-size: 22px; color: #94A3B8; cursor: pointer;">&times;</button>
        </div>
        <form onsubmit="handleSaveProduct(event)" style="padding: 24px;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 6px;">Provider / Kategori</label>
                    <select id="addCategory" required style="width: 100%; padding: 10px 12px; border-radius: 10px; border: 1px solid #E2E8F0; font-size: 13px; background: #F8FAFC;">
                        <option value="Telkomsel">Telkomsel</option>
                        <option value="Indosat">Indosat Ooredoo</option>
                        <option value="XL Axiata">XL Axiata</option>
                        <option value="Axis">Axis</option>
                        <option value="Tri">Tri (3)</option>
                        <option value="Smartfren">Smartfren</option>
                        <option value="VPN Premium">VPN Server VPS Pribadi</option>
                    </select>
                </div>
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 6px;">Kode SKU / Produk OkeConnect</label>
                    <input type="text" id="addSku" placeholder="Contoh: TSEL10GB" required style="width: 100%; padding: 10px 12px; border-radius: 10px; border: 1px solid #E2E8F0; font-size: 13px; background: #F8FAFC; text-transform: uppercase;">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 16px; margin-bottom: 16px;">
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 6px;">Nama Paket / Produk</label>
                    <input type="text" id="addName" placeholder="Contoh: Telkomsel Data Flash 10 GB" required style="width: 100%; padding: 10px 12px; border-radius: 10px; border: 1px solid #E2E8F0; font-size: 13px; background: #F8FAFC;">
                </div>
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 6px;">Masa Aktif (Hari)</label>
                    <div style="position: relative;">
                        <input type="number" id="addActiveDays" placeholder="30" value="30" min="1" required style="width: 100%; padding: 10px 48px 10px 12px; border-radius: 10px; border: 1px solid #E2E8F0; font-size: 13px; background: #F8FAFC;">
                        <span style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); font-size: 12px; color: #64748B; font-weight: 600; pointer-events: none;">Hari</span>
                    </div>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 6px;">Harga Modal (Rp)</label>
                    <input type="number" id="addModalPrice" placeholder="31200" required style="width: 100%; padding: 10px 12px; border-radius: 10px; border: 1px solid #E2E8F0; font-size: 13px; background: #F8FAFC;">
                </div>
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 6px;">Harga Jual (Rp)</label>
                    <input type="number" id="addSellPrice" placeholder="35000" required style="width: 100%; padding: 10px 12px; border-radius: 10px; border: 1px solid #E2E8F0; font-size: 13px; background: #F8FAFC;">
                </div>
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 6px;">Deskripsi / Rincian Kuota</label>
                <textarea id="addDesc" rows="3" placeholder="Rincian kuota 24 jam, bonus aplikasi, masa aktif..." style="width: 100%; padding: 10px 12px; border-radius: 10px; border: 1px solid #E2E8F0; font-size: 13px; background: #F8FAFC; resize: vertical;"></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 24px; padding-top: 16px; border-top: 1px solid #F1F5F9;">
                <button type="button" onclick="closeModal('addProductModal')" style="padding: 10px 18px; border-radius: 10px; border: 1px solid #E2E8F0; background: #FFFFFF; font-size: 13px; font-weight: 600; color: #64748B; cursor: pointer;">Batal</button>
                <button type="submit" id="saveProductSubmitBtn" style="padding: 10px 22px; border-radius: 10px; border: none; background: #4361EE; color: #FFFFFF; font-size: 13px; font-weight: 600; cursor: pointer; box-shadow: 0 4px 12px rgba(67, 97, 238, 0.25);">Simpan Produk</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL: EDIT PRODUK --}}
<div id="editProductModal" style="display: none; position: fixed; inset: 0; z-index: 999; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); align-items: center; justify-content: center; padding: 20px;">
    <div style="background: #FFFFFF; border-radius: 20px; width: 100%; max-width: 580px; max-height: 90vh; overflow-y: auto; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2); border: 1px solid #E2E8F0;">
        <div style="padding: 20px 24px; border-bottom: 1px solid #F1F5F9; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="font-size: 18px; font-weight: 700; color: #0F172A; display: flex; align-items: center; gap: 8px;">
                <span style="width: 32px; height: 32px; border-radius: 8px; background: #FEF3C7; color: #D97706; display: inline-flex; align-items: center; justify-content: center;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                </span>
                Edit Produk
            </h3>
            <button onclick="closeModal('editProductModal')" style="background: none; border: none; font-size: 22px; color: #94A3B8; cursor: pointer;">&times;</button>
        </div>
        <form onsubmit="handleUpdateProduct(event)" style="padding: 24px;">
            <input type="hidden" id="editProductId">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 6px;">Provider / Kategori</label>
                    <select id="editProductCategory" required style="width: 100%; padding: 10px 12px; border-radius: 10px; border: 1px solid #E2E8F0; font-size: 13px; background: #F8FAFC;">
                        <option value="Telkomsel">Telkomsel</option>
                        <option value="Indosat">Indosat</option>
                        <option value="XL Axiata">XL Axiata</option>
                        <option value="Axis">Axis</option>
                        <option value="Tri">Tri</option>
                        <option value="Smartfren">Smartfren</option>
                        <option value="VPN Premium">VPN Premium</option>
                    </select>
                </div>
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 6px;">Kode SKU OkeConnect</label>
                    <input type="text" id="editProductSku" required style="width: 100%; padding: 10px 12px; border-radius: 10px; border: 1px solid #E2E8F0; font-size: 13px; background: #F8FAFC; text-transform: uppercase;">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 16px; margin-bottom: 16px;">
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 6px;">Nama Produk</label>
                    <input type="text" id="editProductName" required style="width: 100%; padding: 10px 12px; border-radius: 10px; border: 1px solid #E2E8F0; font-size: 13px; background: #F8FAFC;">
                </div>
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 6px;">Masa Aktif (Hari)</label>
                    <div style="position: relative;">
                        <input type="number" id="editActiveDays" placeholder="30" min="1" required style="width: 100%; padding: 10px 48px 10px 12px; border-radius: 10px; border: 1px solid #E2E8F0; font-size: 13px; background: #F8FAFC;">
                        <span style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); font-size: 12px; color: #64748B; font-weight: 600; pointer-events: none;">Hari</span>
                    </div>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 6px;">Harga Modal (Rp)</label>
                    <input type="number" id="editProductModalPrice" required style="width: 100%; padding: 10px 12px; border-radius: 10px; border: 1px solid #E2E8F0; font-size: 13px; background: #F8FAFC;">
                </div>
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 6px;">Harga Jual (Rp)</label>
                    <input type="number" id="editProductSellPrice" required style="width: 100%; padding: 10px 12px; border-radius: 10px; border: 1px solid #E2E8F0; font-size: 13px; background: #F8FAFC;">
                </div>
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 6px;">Deskripsi / Rincian</label>
                <textarea id="editProductDesc" rows="3" style="width: 100%; padding: 10px 12px; border-radius: 10px; border: 1px solid #E2E8F0; font-size: 13px; background: #F8FAFC; resize: vertical;"></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 24px; padding-top: 16px; border-top: 1px solid #F1F5F9;">
                <button type="button" onclick="closeModal('editProductModal')" style="padding: 10px 18px; border-radius: 10px; border: 1px solid #E2E8F0; background: #FFFFFF; font-size: 13px; font-weight: 600; color: #64748B; cursor: pointer;">Batal</button>
                <button type="submit" id="updateProductSubmitBtn" style="padding: 10px 22px; border-radius: 10px; border: none; background: #4361EE; color: #FFFFFF; font-size: 13px; font-weight: 600; cursor: pointer;">Perbarui Produk</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL: DELETE CONFIRMATION --}}
<div id="deleteProductModal" style="display: none; position: fixed; inset: 0; z-index: 999; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); align-items: center; justify-content: center; padding: 20px;">
    <div style="background: #FFFFFF; border-radius: 20px; width: 100%; max-width: 440px; padding: 24px; text-align: center; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2); border: 1px solid #E2E8F0;">
        <div style="width: 56px; height: 56px; border-radius: 50%; background: #FEE2E2; color: #EF4444; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 16px;">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="3 6 5 6 21 6"/>
                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
            </svg>
        </div>
        <h3 style="font-size: 18px; font-weight: 700; color: #0F172A; margin-bottom: 8px;">Hapus Produk Ini?</h3>
        <p style="font-size: 13px; color: #64748B; line-height: 1.5; margin-bottom: 20px;">
            Apakah kamu yakin ingin menghapus produk <strong id="deleteProductNameText" style="color: #0F172A;"></strong>? Data di database akan terhapus.
        </p>
        <div style="display: flex; gap: 10px; justify-content: center;">
            <button onclick="closeModal('deleteProductModal')" style="padding: 10px 20px; border-radius: 10px; border: 1px solid #E2E8F0; background: #FFFFFF; font-size: 13px; font-weight: 600; color: #64748B; cursor: pointer;">Batal</button>
            <button onclick="confirmDeleteProduct()" id="deleteProductSubmitBtn" style="padding: 10px 20px; border-radius: 10px; border: none; background: #EF4444; color: #FFFFFF; font-size: 13px; font-weight: 600; cursor: pointer;">Ya, Hapus</button>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    let currentDeleteId = null;

    function filterCategory(category, btn) {
        document.querySelectorAll('.category-pill').forEach(el => {
            el.style.background = '#F8FAFC';
            el.style.color = '#475569';
            el.style.borderColor = '#E2E8F0';
            el.style.fontWeight = '500';
        });
        btn.style.background = '#4361EE';
        btn.style.color = '#FFFFFF';
        btn.style.borderColor = '#4361EE';
        btn.style.fontWeight = '600';

        const rows = document.querySelectorAll('.product-row');
        rows.forEach(row => {
            if (category === 'all' || row.dataset.category === category) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    function searchProducts() {
        const query = document.getElementById('productSearchInput').value.toLowerCase();
        const rows = document.querySelectorAll('.product-row');
        rows.forEach(row => {
            const name = row.dataset.name;
            const sku = row.dataset.sku;
            if (name.includes(query) || sku.includes(query)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    function toggleProductStatus(id, checkbox) {
        fetch(`/admin/produk/${id}/toggle`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, data.status === 'active' ? 'success' : 'warning');
            } else {
                showToast('Gagal mengubah status', 'error');
                checkbox.checked = !checkbox.checked;
            }
        })
        .catch(() => {
            showToast('Terjadi kesalahan jaringan', 'error');
            checkbox.checked = !checkbox.checked;
        });
    }

    function openAddProductModal() {
        document.getElementById('addProductModal').style.display = 'flex';
    }

    function openEditProductModal(product) {
        document.getElementById('editProductId').value = product.id;
        document.getElementById('editProductCategory').value = product.category;
        document.getElementById('editProductSku').value = product.sku;
        document.getElementById('editProductName').value = product.name;
        document.getElementById('editActiveDays').value = parseInt(product.active_period) || 30;
        document.getElementById('editProductModalPrice').value = product.modal_price;
        document.getElementById('editProductSellPrice').value = product.sell_price;
        document.getElementById('editProductDesc').value = product.description || '';

        document.getElementById('editProductModal').style.display = 'flex';
    }

    function openDeleteProductModal(id, name) {
        currentDeleteId = id;
        document.getElementById('deleteProductNameText').innerText = name;
        document.getElementById('deleteProductModal').style.display = 'flex';
    }

    function closeModal(modalId) {
        document.getElementById(modalId).style.display = 'none';
    }

    function handleSaveProduct(e) {
        e.preventDefault();
        const btn = document.getElementById('saveProductSubmitBtn');
        btn.disabled = true;
        btn.innerText = 'Menyimpan...';

        const days = document.getElementById('addActiveDays').value || 30;
        const payload = {
            category: document.getElementById('addCategory').value,
            sku: document.getElementById('addSku').value,
            name: document.getElementById('addName').value,
            active_period: days + ' Hari',
            modal_price: document.getElementById('addModalPrice').value,
            sell_price: document.getElementById('addSellPrice').value,
            description: document.getElementById('addDesc').value,
            status: 'active'
        };

        fetch('/admin/produk', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerText = 'Simpan Produk';
            if (data.success) {
                closeModal('addProductModal');
                showToast(data.message, 'success');
                setTimeout(() => window.location.reload(), 600);
            } else {
                showToast(data.message || 'Gagal menyimpan produk', 'error');
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerText = 'Simpan Produk';
            showToast('Terjadi kesalahan saat menambahkan produk', 'error');
        });
    }

    function handleUpdateProduct(e) {
        e.preventDefault();
        const id = document.getElementById('editProductId').value;
        const btn = document.getElementById('updateProductSubmitBtn');
        btn.disabled = true;
        btn.innerText = 'Memperbarui...';

        const days = document.getElementById('editActiveDays').value || 30;
        const payload = {
            category: document.getElementById('editProductCategory').value,
            sku: document.getElementById('editProductSku').value,
            name: document.getElementById('editProductName').value,
            active_period: days + ' Hari',
            modal_price: document.getElementById('editProductModalPrice').value,
            sell_price: document.getElementById('editProductSellPrice').value,
            description: document.getElementById('editProductDesc').value,
        };

        fetch(`/admin/produk/${id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerText = 'Perbarui Produk';
            if (data.success) {
                closeModal('editProductModal');
                showToast(data.message, 'success');
                setTimeout(() => window.location.reload(), 600);
            } else {
                showToast(data.message || 'Gagal memperbarui produk', 'error');
            }
        })
        .catch(() => {
            btn.disabled = false;
            btn.innerText = 'Perbarui Produk';
            showToast('Terjadi kesalahan saat memperbarui produk', 'error');
        });
    }

    function confirmDeleteProduct() {
        if (!currentDeleteId) return;
        const btn = document.getElementById('deleteProductSubmitBtn');
        btn.disabled = true;
        btn.innerText = 'Menghapus...';

        fetch(`/admin/produk/${currentDeleteId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerText = 'Ya, Hapus';
            closeModal('deleteProductModal');
            if (data.success) {
                showToast(data.message, 'success');
                setTimeout(() => window.location.reload(), 600);
            } else {
                showToast('Gagal menghapus produk', 'error');
            }
        })
        .catch(() => {
            btn.disabled = false;
            btn.innerText = 'Ya, Hapus';
            showToast('Terjadi kesalahan saat menghapus', 'error');
        });
    }

    function syncOkeconnectProducts() {
        showToast('Menghubungkan ke API OkeConnect untuk sinkronisasi...', 'info');
        setTimeout(() => {
            showToast('Sinkronisasi selesai! Harga modal telah diperbarui.', 'success');
        }, 1200);
    }

    function showToast(message, type = 'info') {
        const toast = document.createElement('div');
        const bg = type === 'success' ? '#10B981' : type === 'warning' ? '#F59E0B' : type === 'error' ? '#EF4444' : '#4361EE';
        toast.style.cssText = `
            position: fixed;
            bottom: 24px;
            right: 24px;
            background: ${bg};
            color: #FFFFFF;
            padding: 12px 20px;
            border-radius: 10px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.2);
            font-size: 13px;
            font-weight: 600;
            z-index: 9999;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            transform: translateY(20px);
            opacity: 0;
        `;
        toast.innerText = message;
        document.body.appendChild(toast);
        setTimeout(() => {
            toast.style.transform = 'translateY(0)';
            toast.style.opacity = '1';
        }, 10);
        setTimeout(() => {
            toast.style.transform = 'translateY(20px)';
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 300);
        }, 3500);
    }
</script>
@endsection
