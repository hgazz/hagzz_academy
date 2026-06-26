USE hagzz;

CREATE TABLE IF NOT EXISTS academy_competitions (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  academy_id BIGINT UNSIGNED NOT NULL,
  sport_id BIGINT UNSIGNED NULL,
  home_team_name VARCHAR(255) NOT NULL,
  opponent_name VARCHAR(255) NOT NULL,
  competition_date DATE NOT NULL,
  starts_at TIME NULL,
  venue VARCHAR(255) NULL,
  status ENUM('scheduled','completed','cancelled') NOT NULL DEFAULT 'scheduled',
  home_score SMALLINT UNSIGNED NULL,
  opponent_score SMALLINT UNSIGNED NULL,
  result_notes TEXT NULL,
  notes TEXT NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  INDEX academy_competitions_academy_date_index (academy_id, competition_date),
  INDEX academy_competitions_sport_status_index (sport_id, status),
  CONSTRAINT academy_competitions_academy_id_foreign
    FOREIGN KEY (academy_id) REFERENCES academies(id) ON DELETE CASCADE,
  CONSTRAINT academy_competitions_sport_id_foreign
    FOREIGN KEY (sport_id) REFERENCES sports(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS academy_competition_players (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  academy_competition_id BIGINT UNSIGNED NOT NULL,
  academy_student_id BIGINT UNSIGNED NOT NULL,
  role VARCHAR(255) NULL,
  notes TEXT NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  UNIQUE KEY academy_competition_player_unique (academy_competition_id, academy_student_id),
  CONSTRAINT academy_competition_players_competition_fk
    FOREIGN KEY (academy_competition_id) REFERENCES academy_competitions(id) ON DELETE CASCADE,
  CONSTRAINT academy_competition_players_student_fk
    FOREIGN KEY (academy_student_id) REFERENCES academy_students(id) ON DELETE CASCADE
);

SET @migration_batch = COALESCE((SELECT MAX(batch) + 1 FROM migrations), 1);

INSERT INTO migrations (migration, batch)
SELECT '2026_06_26_000001_create_academy_competitions_table', @migration_batch
WHERE NOT EXISTS (
  SELECT 1 FROM migrations WHERE migration = '2026_06_26_000001_create_academy_competitions_table'
);

INSERT INTO migrations (migration, batch)
SELECT '2026_06_26_000002_create_academy_competition_players_table', @migration_batch
WHERE NOT EXISTS (
  SELECT 1 FROM migrations WHERE migration = '2026_06_26_000002_create_academy_competition_players_table'
);
