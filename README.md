# 1. Установка

## 1.1. Требования к предустановленному ПО

- docker
- php 8.2 (возможно будет работать и на более новых версиях)
- mysql 8.0 (проверено на образе для докера `percona:8.0.36-28`)

## 1.2. Настройка коннекта к БД (не обязательно, только для сохранения в БД, см. п. 2.3)

**1.2.1. Скопировать пример конфига и заполнить своими константами**
```sh
mv config.sample.php config.php
```
После этого откройте файл `config.php` и заполните его своими данными доступа.

**1.2.3. Установить composer, если еще не установлен**

Лучше если вы все сделаете по документации, но если лень искать, то вот команда:
```sh
curl -sS https://getcomposer.org/installer | php
```

**1.2.4. Установить зависимости**
```sh
php composer.phar install
```

**1.2.5. Запустить миграции**
```sh
php vendor/bin/phinx migrate
```

# 2. Сборка текстов и аудио Библии

## 2.1. Скачивание текста перевода

Скрипт `parse.php` нужен для того, чтобы скачать разные версии переводов Библии с сайта bible.by
Пример запуска скрипта для скачивания синодального перевода:
```sh
php parse.php syn
```

С сайта bible.com:
```sh
php parse_com.use.php bti
```

Результат будет скачен с ресурса https://bible.by/syn/ в виде файла JSON и сохранен в директории `bible`.

## 2.2. Добавление таймкодов к аудио (через MFA)

Использует [MontrealCorpusTools](https://github.com/MontrealCorpusTools/) для принудительного выравнивания. 

```sh
php timecodes_mfa.php syn syn-bondarenko MODE_CHANGE
```

Последний аргумент используется для определения поведения в случае наличия файлов:
- `MODE_REPLACE` - все файлы будут удалены, заново скачаны и выровнены (плохо протестированный вариант)
- `MODE_CHANGE` - имеющиеся файлы не будут качаться заново; полностью выровненные книги будут пропущены (экономит время для случаев, когда предыдущий запуск скрипта выпадал)
- `MODE_FINISH` - оптимально быстро доделает фиксы, если предыдущий запуск дошел почти до конца

JSON с таймкодами будет сохранен в папке `audio`.

Скрипт выполняется по несколько часов на каждую версию озвучки.

## 2.3. Сохранение текстов и ссылок в БД

Первый параметр означает тип экспорта и может представлять один из вариантов:
- `TEXT` - сохранение текстовой версии
- `TIMECODES` - сохранение таймкодов

Сохранение текста:
```sh
php save_to_db.php TEXT syn
```

Сохранение выравнивания:
```sh
php save_to_db.php TIMECODES nrt new-russian
```

## 2.4. Запуск тестов

```sh
./vendor/bin/phpunit --bootstrap vendor/autoload.php tests/BibleParserTest.php
```

# 3. Возможно пригодятся команды

## 3.1. Ручной запуск выравнивания

Иногда так бывает, что команда mfa часть файлов внаглую пропускает и даже ошибок не выдает. 
В скрипте выравнивания есть механизм добора и допроведения этих файлов с увеличенным `retry_beam`, но и он не всегда помогает.

Можно посмотреть лог (название файла с логом будет в конце вывода ошибки):
```
docker exec -it mfa bash -c "cat /mfa/mfa_input/alignment/log/align.1.log"
```

Возможно в текстовой расшифровке что-то сильно не соответствует записи (например, опущено название главы или что-то добавлено). Попробуйте вручную исправить файл и перезапустить выравнивание. 

Можно доделать их вручную. 

3.1.1. Запуск контейнера для ручного выравнивания (но вообще-то он остается и так запущенным после скрипта timecodes_mfa.php):
```
docker run -it -d --name mfa --volume "/path/to/audio:/audio" mmcauliffe/montreal-forced-aligner:v2.2.17
docker exec -it mfa bash -c "mfa models download dictionary russian_mfa --version v2.0.0a"
docker exec -it mfa bash -c "mfa models download acoustic russian_mfa --version v2.0.0a"
```

3.1.2. Выравнивание директории:
```
docker exec -it mfa bash -c "mfa align --clean --overwrite --output_format json /audio/test_in russian_mfa russian_mfa /audio/test_out --beam 40 --retry_beam 160"
```

3.1.3. Сбор результатов:
- закомментировать строку `mfa_align_all($translation, $voice, $mode);` в `timecodes_mfa.php`
- запустить сборку `php82 timecodes_mfa.php syn syn-bondarenko MODE_CHANGE`
- раскомментрировать обратно указанную строку

## 3.2. Полезные ссылки на документацию:
- [https://montreal-forced-aligner.readthedocs.io/en/latest/user_guide/workflows/alignment.html#pretrained-alignment](Страница доки MFA по команде выравнивания)

-------------------------------------------------------------

## 2-aльтернатива. Добавление таймкодов к аудио (через aenaes, устар.)

Выравнивает плюс-минус километр. Лучше использовать MFA (см. выше).

Скрипт позволяет скачивать аудиоверсию Библии и создавать файл с таймкодами каждого стиха. 

На машине должен быть установлен докер и иметься [образ aeneas](https://github.com/MariaPaypoint/aeneas-docker).
Также предварительно должен быть сгенерирован файл с текстами (см. выше "Скачивание переводов Библии").

Примеры:
```
php timecodes_aenaes.php syn syn-bondarenko MODE_REPLACE
php timecodes_aenaes.php bti prozorovsky MODE_CHANGE
```
Аудиофайлы и JSON с таймкодами будет сохранен в папке `audio`.

Примечание. В проекте хранятся только примеры результирующих файлов - по 2 главы первых 2 книг синодального перевода (аудиоверсия в замечательно озвучке украинского актёра театра и кино, народного артиста Украины Александра Бондаренко).
