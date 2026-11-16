# Daily Mall — 세션 하이재킹 실습용 취약 웹 쇼핑몰

웹보안 팀 프로젝트(MITM 기반 세션 하이재킹 공격·방어)에서 **공격 대상**으로 쓰는 쇼핑몰 웹앱이다.
로그인하면 세션 쿠키(`PHPSESSID`) 하나로 로그인 상태가 유지되는데, 보안 설정을 일부러 해두지 않아서
**쿠키만 탈취하면 비밀번호 없이 계정이 그대로 넘어간다.**

> ⚠️ 이 웹앱은 보안 교육·실습용으로 **일부러 취약하게** 만든 것이다. 실제 서비스로 사용하지 말 것.

## 환경

- **Nginx + PHP** (DB 없이 JSON 파일로 데이터 저장)
- 기본 제공 계정 없음 — 실습 전 회원가입으로 **피해자 계정**을 만든다 (예: `victim` / `password123`)

## 디렉터리 구조

```
web_security_kgu/
├── nginx/
│   └── default.conf          # Nginx 설정
├── src/                      # PHP 소스 전체
│   ├── config.php            # 설정 + 저장소 함수 (세션 쿠키 설정도 여기)
│   ├── index.php             # 첫 진입 분기
│   ├── login.php             # 로그인
│   ├── register.php          # 회원가입
│   ├── logout.php            # 로그아웃
│   ├── products.php          # 상품 목록 (메인 페이지)
│   ├── cart.php              # 장바구니
│   ├── order.php             # 주문 처리/완료
│   ├── mypage.php            # 마이페이지 (주문 내역)
│   ├── board.php             # 상품 후기 게시판
│   └── data/                 # 자동 생성 (깃에 올리지 않음)
│       ├── users.json        # 회원 (비밀번호 평문)
│       ├── products.json     # 상품 목록
│       ├── orders.json       # 주문 내역
│       └── posts.json        # 후기 글
├── docker-compose.yml
└── README.md
```

## 페이지 설명

| 파일 | 설명 | 로그인 필요 |
|------|------|:---:|
| `config.php` | 세션 쿠키 설정 + 사용자/상품/장바구니/주문/게시판 저장 함수 (화면 없음) | - |
| `index.php` | 로그인 상태면 상품 페이지로, 아니면 로그인 페이지로 분기 | - |
| `register.php` | 회원가입 → `users.json`에 저장 | ❌ |
| `login.php` | 로그인 폼 + 처리, 성공 시 `PHPSESSID` 발급 | ❌ |
| `logout.php` | 세션 파기 + 쿠키 삭제 | ✅ |
| `products.php` | 상품 목록 + 장바구니 담기 (메인 페이지) | ✅ |
| `cart.php` | 장바구니 확인/삭제 → 주문하기 | ✅ |
| `order.php` | 주문 확정(가짜 결제) → `orders.json` 저장 | ✅ |
| `mypage.php` | 로그인한 계정의 주문 내역 표시 | ✅ |
| `board.php` | 상품 후기 작성/목록 | ✅ |

## 취약하게 둔 부분 (실습 포인트)

- **HTTP(평문)로 서비스** — HTTPS 미적용 → 스니핑으로 쿠키 노출
- **세션 쿠키에 Secure / HttpOnly / SameSite 미설정** — 보호 장치 없음
- **로그인 후에도 세션 ID 재발급 안 함** — 탈취한 세션이 계속 유효
- **접속 IP / User-Agent 검사 없음** — 다른 기기에서 훔친 쿠키가 그대로 통함
- **세션 쿠키만 있으면 인증 통과** — 비밀번호 재확인 없음 (쿠키 탈취 = 계정 장악)
- **비밀번호 평문 저장** (`users.json`)

## 실행

```bash
# Docker
docker compose up -d          # http://<서버IP>/

# 또는 PHP 내장 서버
php -S 0.0.0.0:80 -t src
```

접속: `http://<서버IP>/` → 회원가입 → 로그인 → 상품/장바구니/주문/게시판 이용

> `src/data/` 폴더에 PHP가 쓰기 권한이 있어야 회원가입·주문·후기가 저장된다.
> 저장이 안 되면: `chmod -R 777 src/data`

## 세션 하이재킹 시연 흐름 (요약)

1. 피해자가 Daily Mall에 로그인 → `PHPSESSID` 쿠키 발급
2. 공격자가 같은 네트워크에서 ARP 스푸핑으로 중간자 위치 확보
3. 평문 HTTP 트래픽에서 `PHPSESSID` 쿠키 탈취 (Wireshark)
4. 공격자 브라우저에 쿠키 주입 → **로그인 없이 피해자 계정 접근**
   (주문 내역·장바구니 열람, 피해자 명의로 주문 등)
5. 방어(HTTPS+HSTS, Secure 쿠키, 세션 재발급, 이상행위 탐지) 적용 후 동일 공격이 차단됨을 검증