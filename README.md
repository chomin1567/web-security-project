# 세션 하이재킹 실습용 로그인 웹앱

웹보안 팀 프로젝트에서 세션 하이재킹 시연에 쓰는 로그인 웹앱이다.
로그인하면 세션 쿠키(`PHPSESSID`) 하나로 로그인 상태가 유지되는데, 보안 설정을 해두지 않아서 쿠키만 탈취하면 계정이 그대로 넘어간다.

## 환경
- Nginx + PHP
- 로그인 계정: `victim` / `password123` (`src/config.php`)

## 페이지
- `login.php` : 로그인 폼 + 처리, 성공 시 `PHPSESSID` 발급
- `mypage.php` : 로그인한 사용자만 접근, 세션 없으면 로그인 페이지로 이동, 계정명과 세션 ID 표시
- `logout.php` : 로그아웃
- `index.php` : 로그인 상태에 따라 분기

## 취약하게 둔 부분
- HTTP(평문)로 서비스 (HTTPS 미적용)
- 세션 쿠키에 Secure / HttpOnly / SameSite 미설정
- 로그인 후에도 세션 ID 재발급 안 함
- 접속 IP / User-Agent 검사 없음
- 세션 쿠키만 있으면 인증 통과 (비밀번호 재확인 없음)

## 실행
```bash
# Docker
docker compose up -d        # http://<서버IP>/

# 또는 PHP 내장 서버
php -S 0.0.0.0:80 -t src
```
