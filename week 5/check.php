<?php
// Run: php "week 5/check.php" (requires mbstring, no database needed).
require __DIR__ . '/app/common/functions.php';

function check($condition, $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

[$product, $errors] = validateProduct(['name' => ' Product ', 'price' => '0.01', 'quantity' => '0']);
check(!$errors && $product['name'] === 'Product', 'Valid product rejected.');
foreach ([
    ['name' => '', 'price' => '1', 'quantity' => '0'],
    ['name' => str_repeat("\u{00E9}", 101), 'price' => '1', 'quantity' => '0'],
    ['name' => 'A', 'price' => '0', 'quantity' => '0'],
    ['name' => 'A', 'price' => '100000000', 'quantity' => '0'],
    ['name' => 'A', 'price' => '1.001', 'quantity' => '0'],
    ['name' => 'A', 'price' => '1', 'quantity' => '-1'],
    ['name' => 'A', 'price' => '1', 'quantity' => '1.5'],
    ['name' => [], 'price' => [], 'quantity' => []],
] as $input) {
    check((bool) validateProduct($input)[1], 'Invalid product accepted.');
}
check(escape('<script>"') === '&lt;script&gt;&quot;', 'HTML escaping failed.');
$_SESSION = ['csrf_token' => 'test-token'];
$_POST = ['csrf_token' => 'wrong-token'];
check(!validCsrfToken(), 'Invalid CSRF token accepted.');
$_POST['csrf_token'] = 'test-token';
check(validCsrfToken(), 'Valid CSRF token rejected.');
$sql = file_get_contents(__DIR__ . '/sql/query.sql');
check((bool) preg_match('/\$2y\$[^\x27]+/', $sql, $matches), 'Sample password hash missing.');
check(password_verify('123456', $matches[0]), 'Sample password does not match.');
check(!password_verify('incorrect', $matches[0]), 'Wrong password accepted.');
echo "PASS: product validation, HTML escaping, CSRF, sample password hash.\n";
