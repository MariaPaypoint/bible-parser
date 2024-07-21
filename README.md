# Сборка текстов и аудио Библии

## 1. Скачивание текста перевода

Скрипт нужен для того, чтобы скачать разные версии переводов Библии с сайта bible.by

Пример запуска скрипта для скачивания синодального перевода:
```
php parse.php syn
```

Результат будет скачен с ресурса https://bible.by/syn/ в виде файла JSON и сохранен в директории `bible`.

## 2. Добавление таймкодов к аудио (через MFA)

Использует [MontrealCorpusTools](https://github.com/MontrealCorpusTools/) для принудительного выравнивания. 

```
php82 timecodes_mfa.php syn bondarenko MODE_REPLACE
```

Последний аргумент используется для определения поведения в случае наличия файлов:
- `MODE_REPLACE` - все файлы будут удалены, заново скачаны и выровнены
- `MODE_ADD` - все что есть будет оставлено как есть, а недостающее добавлено
- `MODE_CHANGE` - ???

JSON с таймкодами будет сохранен в папке `audio`.

## 3. Сохранение текстов и ссылок в БД

???

-------------------------------------------------------------

## 2a. Добавление таймкодов к аудио (через aenaes)

Выравнивает плюс-минус километр. Лучше использовать MFA (см. выше).

Скрипт позволяет скачивать аудиоверсию Библии и создавать файл с таймкодами каждого стиха. 

На машине должен быть установлен докер и иметься [образ aeneas](https://github.com/MariaPaypoint/aeneas-docker).
Также предварительно должен быть сгенерирован файл с текстами (см. выше "Скачивание переводов Библии").

Примеры:
```
php timecodes.php syn bondarenko MODE_REPLACE
php timecodes.php bti prozorovsky MODE_CHANGE
```
Аудиофайлы и JSON с таймкодами будет сохранен в папке `audio`.

Примечание. В проекте хранятся только примеры результирующих файлов - по 2 главы первых 2 книг синодального перевода (аудиоверсия в замечательно озвучке украинского актёра театра и кино, народного артиста Украины Александра Бондаренко).
