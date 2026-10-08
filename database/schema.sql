CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role VARCHAR(10) NOT NULL,
  class_id INT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE classes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  class_name VARCHAR(100) NOT NULL,
  class_code VARCHAR(10) NOT NULL UNIQUE,
  teacher_id INT NOT NULL,
  FOREIGN KEY (teacher_id) REFERENCES users(id)
);

CREATE TABLE symbols (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  meaning VARCHAR(255) NOT NULL,
  proverb VARCHAR(255) NULL,
  image VARCHAR(100) NOT NULL,
  level INT NOT NULL
);

CREATE TABLE games (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  level INT NOT NULL,
  grid_size INT NOT NULL,
  completed TINYINT DEFAULT 0,
  played_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE quiz_attempts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  score INT NOT NULL,
  total INT NOT NULL,
  taken_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE user_symbols (
  user_id INT NOT NULL,
  symbol_id INT NOT NULL,
  PRIMARY KEY (user_id, symbol_id),
  FOREIGN KEY (user_id) REFERENCES users(id),
  FOREIGN KEY (symbol_id) REFERENCES symbols(id)
);

INSERT INTO symbols (name, meaning, image, level) VALUES
('Gye Nyame', 'Except God - the supremacy of God', 'gye_nyame.png', 1),
('Sankofa', 'Go back and get it - learning from the past', 'sankofa.png', 1),
('Dwennimmen', 'Ram''s horns - humility and strength', 'dwennimmen.png', 1),
('Akoma', 'The heart - patience and tolerance', 'akoma.png', 1),
('Adinkrahene', 'Chief of Adinkra symbols - greatness and leadership', 'adinkrahene.png', 2),
('Eban', 'Fence - love, safety and security', 'eban.png', 2);