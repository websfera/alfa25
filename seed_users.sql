-- Seed testovacich uzivatelu pro rychle zkouseni konverzaci v messengeru.
-- Vsechny ucty maji heslo: heslo123

START TRANSACTION;

INSERT INTO `user` (
    `uuid`,
    `username`,
    `email`,
    `password`,
    `phone`,
    `first_name`,
    `last_name`,
    `gender`,
    `birthdate`
)
VALUES
    (
        UUID_TO_BIN('018f28f0-6d98-70b2-a02f-fb7e7b9f6a11'),
        'alice',
        'alice@test.cz',
        '$2y$12$lb8l0w0TAtVjbkpkTRUJjuzdCAhrIHbehtkX5uye0M2/xfrZof5Sm',
        777111222,
        'Alice',
        'Novakova',
        'f',
        '2001-04-12'
    ),
    (
        UUID_TO_BIN('018f28f0-6d98-70b2-a02f-fb7e7b9f6a22'),
        'bob',
        'bob@test.cz',
        '$2y$12$ejS9j2Iy6o2phB1q8iZ5rOJ46CN2wWQgKxhdMRuobN.Vg3hCTSFLq',
        777333444,
        'Bob',
        'Svoboda',
        'm',
        '1999-09-03'
    ),
    (
        UUID_TO_BIN('018f28f0-6d98-70b2-a02f-fb7e7b9f6a33'),
        'klara',
        'klara@test.cz',
        '$2y$12$y/6HntWcOEcHbzphjlDqluvYDNacIjGD0F201pWJiIwpXimysRxde',
        777555666,
        'Klara',
        'Dvorakova',
        'f',
        '2002-01-21'
    )
ON DUPLICATE KEY UPDATE
    `email` = VALUES(`email`),
    `password` = VALUES(`password`),
    `phone` = VALUES(`phone`),
    `first_name` = VALUES(`first_name`),
    `last_name` = VALUES(`last_name`),
    `gender` = VALUES(`gender`),
    `birthdate` = VALUES(`birthdate`),
    `updated_at` = NOW();

COMMIT;
