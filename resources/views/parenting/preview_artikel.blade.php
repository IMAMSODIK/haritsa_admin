<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <title>Artikel Parenting · {{$article['title']}}</title>
    <!-- Font sederhana & icon -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f4f7fb;
            color: #1e293b;
            line-height: 1.6;
            padding: 0;
            margin: 0;
            -webkit-font-smoothing: antialiased;
        }

        /* wrapper konten utama agar rapi di layar lebar */
        .article-wrapper {
            max-width: 720px;
            width: 90%;
            margin: 2rem auto;
            background: white;
            border-radius: 32px;
            box-shadow: 0 20px 40px -12px rgba(0,20,30,0.15);
            overflow: hidden;
            transition: all 0.2s ease;
        }

        /* area gambar unggulan */
        .hero-image {
            width: 100%;
            height: auto;
            aspect-ratio: 16 / 9;
            background-color: #b8d8e3;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .hero-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        /* label / kategori di atas gambar */
        .category-tag {
            position: absolute;
            top: 16px;
            left: 20px;
            background: rgba(255,255,255,0.9);
            backdrop-filter: blur(4px);
            padding: 6px 16px;
            border-radius: 40px;
            font-size: 0.8rem;
            font-weight: 600;
            letter-spacing: 0.3px;
            color: #0f4c5c;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            border: 1px solid rgba(255,255,255,0.4);
        }

        /* konten artikel */
        .article-content {
            padding: 28px 24px 20px;
        }

        .article-header {
            margin-bottom: 20px;
        }

        .article-header h1 {
            font-size: 2rem;
            font-weight: 700;
            line-height: 1.2;
            letter-spacing: -0.02em;
            color: #0b2b36;
            margin-bottom: 12px;
        }

        .meta {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            align-items: center;
            font-size: 0.9rem;
            color: #547a85;
            border-bottom: 1px solid #e2eef2;
            padding-bottom: 16px;
        }

        .meta i {
            margin-right: 5px;
            color: #3b8b9c;
            width: 18px;
        }

        .meta span {
            display: inline-flex;
            align-items: center;
        }

        /* body teks */
        .body-text p {
            margin-bottom: 1.4rem;
            font-size: 1.05rem;
            color: #264653;
        }

        /* .body-text p:first-child::first-letter {
            font-size: 3.8rem;
            font-weight: 600;
            float: left;
            line-height: 0.8;
            margin-right: 10px;
            color: #1d6f83;
            font-family: 'Times New Roman', serif;
        } */

        .pullquote {
            margin: 2rem 0 1.8rem;
            padding: 1.2rem 1.8rem;
            background: #e6f3f7;
            border-radius: 24px;
            font-style: italic;
            font-weight: 500;
            color: #104d5c;
            border-left: 6px solid #3097b1;
            font-size: 1.2rem;
            box-shadow: inset 0 1px 4px rgba(0,0,0,0.02);
        }

        /* bagian BACA JUGA — artikel lain */
        .more-articles {
            background: #f0f6f9;
            margin-top: 30px;
            padding: 28px 24px 32px;
            border-radius: 0 0 32px 32px;
        }

        .more-articles h2 {
            font-size: 1.5rem;
            font-weight: 600;
            color: #0b3b44;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 24px;
        }

        .more-articles h2 i {
            color: #218298;
            font-size: 1.6rem;
        }

        /* grid card untuk artikel lain — mobile first */
        .article-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
        }

        /* tampilan card */
        .card {
            background: white;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 8px 16px -6px rgba(0,40,50,0.08);
            transition: transform 0.15s ease, box-shadow 0.2s;
            display: flex;
            flex-direction: column;
            border: 1px solid rgba(157, 195, 205, 0.3);
        }

        .card:active {
            transform: scale(0.99);
            background: #f9fdfe;
        }

        .card-img {
            width: 100%;
            aspect-ratio: 16 / 9;
            background-color: #cbdde3;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .card-content {
            padding: 16px 18px 18px;
        }

        .card-category {
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #3097b1;
            margin-bottom: 6px;
        }

        .card-title {
            font-size: 1.2rem;
            font-weight: 700;
            line-height: 1.3;
            margin-bottom: 6px;
            color: #113946;
        }

        .card-excerpt {
            font-size: 0.9rem;
            color: #43646e;
            margin-bottom: 14px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .read-more {
            font-weight: 600;
            font-size: 0.9rem;
            color: #0f6f84;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border-bottom: 1.5px solid transparent;
            transition: border-color 0.2s;
        }

        .read-more i {
            font-size: 0.8rem;
            transition: transform 0.2s;
        }

        .card:hover .read-more i {
            transform: translateX(4px);
        }

        /* footer kecil */
        .site-footer {
            text-align: center;
            padding: 16px 0 24px;
            font-size: 0.8rem;
            color: #7399a5;
        }

        /* responsive untuk layar lebih lebar (tablet) */
        @media screen and (min-width: 480px) {
            .article-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        /* perbaikan untuk layar besar, wrapper tetap 720px */
        @media screen and (min-width: 800px) {
            .article-wrapper {
                width: 720px;
            }
            .article-header h1 {
                font-size: 2.4rem;
            }
        }

        /* touch friendly */
        .card, .read-more, .meta span, .category-tag {
            cursor: default;
        }

        a, .clickable {
            text-decoration: none;
            color: inherit;
        }

        /* tombol back (opsional) */
        .back-nav {
            padding: 8px 20px 0;
            max-width: 720px;
            margin: 0 auto;
            font-size: 0.9rem;
            color: #3b7e8c;
        }
        .back-nav i {
            margin-right: 6px;
        }
        hr {
            border: none;
            border-top: 1px solid #d1e3e8;
            margin: 16px 0 0;
        }
    </style>
</head>
<body>

<main class="article-wrapper">

    <!-- HERO -->
    <div class="hero-image">
        <img src="{{ $article['thumbnailUrl'] ?? 'https://picsum.photos/800/450' }}"
             alt="{{ $article['title'] ?? 'Artikel' }}"
             loading="lazy">

        <div class="category-tag">
            <i class="far fa-compass" style="margin-right: 6px;"></i>
            ARTIKEL
        </div>
    </div>

    <!-- ARTIKEL UTAMA -->
    <article class="article-content">

        <div class="article-header">
            <h1>{{ $article['title'] ?? 'Tanpa Judul' }}</h1>

            <div class="meta">
                <span>
                    <i class="far fa-user-circle"></i>
                    {{ $article['moderator'] ?? 'Admin' }}
                </span>

                <span>
                    <i class="far fa-calendar-alt"></i>
                    {{ \Carbon\Carbon::parse($article['updatedAt'])->translatedFormat('d M Y') ?? '-' }}
                </span>

                <span>
                    <i class="far fa-star"></i>
                    Score: {{ $article['score'] ?? 0 }}
                </span>
            </div>
        </div>

        <!-- BODY HTML dari API -->
        <div class="body-text">
            {!! $article['content'] ?? '<p>Artikel tidak tersedia</p>' !!}
        </div>

    </article>

    <!-- FOOTER STRIP -->
    <div style="height: 4px; background: linear-gradient(90deg, #c2e0e9, transparent);"></div>

</main>

<footer class="site-footer">
    <i class="far fa-copyright"></i>
    {{ date('Y') }} Parenting Article — semua artikel untuk edukasi keluarga
</footer>


</body>
</html>