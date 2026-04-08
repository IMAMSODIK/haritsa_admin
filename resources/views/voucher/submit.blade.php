<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Validasi Kode Voucher | Promo Haritsa</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background: linear-gradient(145deg, #f0f4ff 0%, #e6edf7 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            position: relative;
        }

        body::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            background-image: radial-gradient(circle at 10% 20%, rgba(99, 102, 241, 0.08) 0%, rgba(99, 102, 241, 0.00) 70%);
            pointer-events: none;
        }

        .voucher-card {
            max-width: 560px;
            width: 100%;
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(0px);
            border-radius: 3rem;
            box-shadow: 0 25px 45px -12px rgba(0, 0, 0, 0.25), 0 4px 12px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            border: 1px solid rgba(255, 255, 255, 0.5);
        }

        .voucher-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 30px 50px -18px rgba(0, 0, 0, 0.3);
        }

        .card-header {
            background: linear-gradient(135deg, #4274BA 0%, #366ebb 100%);
            padding: 2rem 2rem 1.8rem;
            text-align: center;
            color: white;
        }

        .badge-icon {
            display: inline-flex;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(4px);
            padding: 0.6rem 1.2rem;
            border-radius: 100px;
            font-size: 0.85rem;
            font-weight: 500;
            letter-spacing: 0.5px;
            margin-bottom: 1rem;
            border: 1px solid rgba(255,255,255,0.25);
        }

        .badge-icon span {
            margin-right: 6px;
            font-size: 1.1rem;
        }

        .card-header h1 {
            font-size: 2.2rem;
            font-weight: 700;
            letter-spacing: -0.3px;
            margin-bottom: 0.5rem;
            word-break: keep-all;
        }

        .card-header p {
            font-size: 1rem;
            opacity: 0.85;
            font-weight: 400;
            max-width: 85%;
            margin: 0 auto;
        }

        .card-body {
            padding: 2rem 1.8rem 2.2rem;
        }

        .input-group {
            margin-bottom: 1.8rem;
        }

        label {
            display: block;
            font-weight: 600;
            font-size: 0.9rem;
            color: #4274BA;
            margin-bottom: 0.6rem;
            letter-spacing: -0.2px;
        }

        .voucher-input-wrapper {
            display: flex;
            align-items: center;
            background: #f9fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 1.8rem;
            transition: all 0.2s ease;
            padding: 0.1rem 0.1rem 0.1rem 1rem;
        }

        .voucher-input-wrapper:focus-within {
            border-color: #4274BA;
            box-shadow: 0 0 0 3px rgba(66, 116, 186, 0.2);
            background: white;
        }

        .prefix-icon {
            color: #6c7a91;
            font-size: 1.2rem;
            margin-right: 0.5rem;
            display: flex;
            align-items: center;
        }

        #voucherCode {
            flex: 1;
            border: none;
            background: transparent;
            padding: 0.9rem 0.2rem 0.9rem 0;
            font-size: 1rem;
            font-weight: 500;
            font-family: 'Inter', monospace;
            letter-spacing: 0.3px;
            color: #4274BA;
            outline: none;
        }

        #voucherCode::placeholder {
            color: #b9c2d4;
            font-weight: 400;
            font-family: 'Inter', system-ui;
            letter-spacing: normal;
        }

        .submit-btn {
            width: 100%;
            background: linear-gradient(105deg, #4274BA 0%, #336cbb 100%);
            color: white;
            border: none;
            padding: 1rem 1.5rem;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 3rem;
            cursor: pointer;
            transition: all 0.25s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 6px 14px rgba(30, 42, 94, 0.25);
            font-family: inherit;
        }

        .submit-btn:hover {
            background: linear-gradient(105deg, #4274BA 0%, #306abb 100%);
            transform: scale(0.98);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .submit-btn:active {
            transform: scale(0.97);
        }

        .message-area {
            margin-top: 1.5rem;
            padding: 0.8rem 1rem;
            border-radius: 1.5rem;
            font-size: 0.9rem;
            font-weight: 500;
            text-align: center;
            background: #f8fafc;
            transition: all 0.2s ease;
            border-left: 4px solid transparent;
        }

        .message-area.success {
            background: #e6f7ec;
            border-left-color: #2e7d32;
            color: #1e5622;
        }

        .message-area.error {
            background: #fee9e6;
            border-left-color: #d32f2f;
            color: #b91c1c;
        }

        .message-area.info {
            background: #eef2ff;
            border-left-color: #4274BA;
            color: #306abb;
        }

        .voucher-hint {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            margin-top: 1.2rem;
            padding-top: 0.8rem;
            border-top: 1px dashed #e2edf2;
            font-size: 0.75rem;
            color: #5b6e8c;
        }

        .demo-badge {
            background: #eef2ff;
            padding: 0.3rem 0.9rem;
            border-radius: 40px;
            font-family: monospace;
            font-weight: 600;
            color: #306abb;
            cursor: pointer;
            transition: background 0.2s;
        }

        .demo-badge:hover {
            background: #dfe6ff;
        }

        @media (max-width: 480px) {
            body {
                padding: 1rem;
            }
            .card-header {
                padding: 1.5rem 1rem;
            }
            .card-header h1 {
                font-size: 1.7rem;
            }
            .card-body {
                padding: 1.5rem;
            }
            .voucher-input-wrapper {
                padding-left: 0.8rem;
            }
            .submit-btn {
                padding: 0.85rem;
                font-size: 1rem;
            }
            .badge-icon {
                font-size: 0.7rem;
                padding: 0.4rem 1rem;
            }
        }

        .loading-spinner {
            display: inline-block;
            width: 18px;
            height: 18px;
            border: 2px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 0.6s linear infinite;
            margin-right: 6px;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        button:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        footer {
            text-align: center;
            font-size: 0.7rem;
            color: #8ba0bc;
            margin-top: 1rem;
            padding: 0 1rem 1rem;
        }
    </style>
</head>
<body>
    <div class="voucher-card">
        <div class="card-header">
            <div class="badge-icon">
                <span><img src="{{ asset('own_assets/logo/logo.png') }}" width="50%" alt=""></span>
            </div>
            <h1>Masukkan Kode Voucher</h1>
            <p>Silahkan masukkan kode untuk memvalidasi voucher</p>
        </div>
        <div class="card-body">
            <div class="input-group">
                <label for="voucherCode">Kode Voucher</label>
                <div class="voucher-input-wrapper">
                    <div class="prefix-icon">🏷️</div>
                    <input type="text" id="voucherCode" name="voucherCode" placeholder="contoh: VOUCHER25" autocomplete="off" spellcheck="false">
                </div>
            </div>
            <button class="submit-btn" id="submitBtn">
                <span>🔓</span> <span id="btnText">Validasi & Klaim</span>
            </button>
            <footer>
                * Setiap kode promo hanya dapat digunakan satu kali per transaksi
            </footer>
        </div>
    </div>

    <script>
        const validVouchers = new Map([
            ['WELCOME10', { discount: '10% Diskon', message: 'Selamat! Kode WELCOME10 berhasil digunakan. Diskon 10% otomatis berlaku.' }],
            ['FREESHIP', { discount: 'Gratis Ongkir', message: 'Hore! Kode FREESHIP valid. Nikmati gratis ongkir tanpa minimum belanja.' }],
            ['VOUCHER25', { discount: 'Rp 25.000', message: 'Berhasil! Kode VOUCHER25 memberikan potongan Rp25.000.' }],
            ['SPECIAL50', { discount: '50% off maks Rp50rb', message: 'Istimewa! SPECIAL50 aktif, potongan 50% maksimal Rp50.000.' }],
            ['MERDEKA', { discount: 'Diskon 17%', message: 'Merdeka! Kode MERDEKA memberikan diskon 17% untuk semua produk.' }],
            ['FLASH40', { discount: 'Flash sale 40%', message: 'Wow! FLASH40 terverifikasi, diskon 40% hanya hari ini.' }]
        ]);

        const voucherInput = document.getElementById('voucherCode');
        const submitBtn = document.getElementById('submitBtn');
        const btnTextSpan = document.getElementById('btnText');
        const messageBox = document.getElementById('messageBox');

        function showMessage(type, text) {
            messageBox.classList.remove('success', 'error', 'info');
            if (type === 'success') {
                messageBox.classList.add('success');
            } else if (type === 'error') {
                messageBox.classList.add('error');
            } else {
                messageBox.classList.add('info');
            }
            messageBox.innerHTML = text;
        }

        function validateVoucher(code) {
            return new Promise((resolve, reject) => {
                setTimeout(() => {
                    const normalizedCode = code.trim().toUpperCase();
                    if (normalizedCode === "") {
                        reject({ type: 'empty', message: '⚠️ Kode voucher tidak boleh kosong. Masukkan kode terlebih dahulu.' });
                        return;
                    }
                    if (validVouchers.has(normalizedCode)) {
                        const voucherData = validVouchers.get(normalizedCode);
                        resolve({
                            valid: true,
                            code: normalizedCode,
                            discount: voucherData.discount,
                            message: voucherData.message
                        });
                    } else {
                        reject({ type: 'invalid', message: `❌ "${code.trim()}" bukan kode voucher yang valid. Coba periksa kembali atau gunakan kode demo.` });
                    }
                }, 200);
            });
        }

        async function handleSubmit() {
            let rawCode = voucherInput.value;
            if (!rawCode.trim()) {
                showMessage('error', '📛 Kode voucher masih kosong. Yuk, isi dengan kode promo!');
                voucherInput.focus();
                return;
            }

            const originalBtnText = btnTextSpan.innerHTML;
            btnTextSpan.innerHTML = '<span class="loading-spinner"></span> Memeriksa...';
            submitBtn.disabled = true;
            
            try {
                const result = await validateVoucher(rawCode);
                showMessage('success', `✅ ${result.message} (Kode: ${result.code} | ${result.discount})`);
                voucherInput.value = result.code;
                
                const wrapper = document.querySelector('.voucher-input-wrapper');
                wrapper.style.transition = '0.2s';
                wrapper.style.borderColor = '#2e7d32';
                wrapper.style.backgroundColor = '#e8f5e9';
                setTimeout(() => {
                    wrapper.style.borderColor = '#e2e8f0';
                    wrapper.style.backgroundColor = '#f9fafc';
                }, 1200);
            } catch (error) {
                let errorMsg = error.message || 'Kode voucher tidak dikenali.';
                if (error.type === 'empty') errorMsg = error.message;
                showMessage('error', errorMsg);
                const wrapper = document.querySelector('.voucher-input-wrapper');
                wrapper.style.transition = '0.15s';
                wrapper.style.borderColor = '#d32f2f';
                wrapper.style.backgroundColor = '#fff5f5';
                setTimeout(() => {
                    wrapper.style.borderColor = '#e2e8f0';
                    wrapper.style.backgroundColor = '#f9fafc';
                }, 800);
                voucherInput.focus();
            } finally {
                btnTextSpan.innerHTML = originalBtnText;
                submitBtn.disabled = false;
            }
        }

        submitBtn.addEventListener('click', handleSubmit);

        voucherInput.addEventListener('keypress', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                handleSubmit();
            }
        });

        const demoBadges = document.querySelectorAll('.demo-badge');
        demoBadges.forEach(badge => {
            badge.addEventListener('click', function(e) {
                const code = this.getAttribute('data-code');
                if (code) {
                    voucherInput.value = code;
                    voucherInput.focus();
                    const wrapper = document.querySelector('.voucher-input-wrapper');
                    wrapper.style.borderColor = '#3b4e9b';
                    wrapper.style.backgroundColor = '#f0f4ff';
                    setTimeout(() => {
                        wrapper.style.borderColor = '#e2e8f0';
                        wrapper.style.backgroundColor = '#f9fafc';
                    }, 600);
                    showMessage('info', `✨ Kode "${code}" siap digunakan. Tekan tombol validasi.`);
                }
            });
        });

        voucherInput.addEventListener('focus', () => {
            if (messageBox.classList.contains('info') && messageBox.innerHTML.includes('Masukkan kode promo')) {
            }
        });


        console.log('Voucher page siap — desain responsif dan modern');
    </script>
</body>
</html>