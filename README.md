# Сборка текстов и аудио Библии

## Скачивание переводов Библии

Скрипт нужен для того, чтобы скачать разные версии переводов Библии с сайта bible.by

Пример запуска скрипта для скачивания синодального перевода:
```
php parse.php syn
```

Результат будет скачен с ресурса https://bible.by/syn/ в виде файла JSON и сохранен в директории `bible`.

## Добавление таймкодов к аудио (через aenaes)

Выравнивает плюс-минус километр. Лучше использовать MFA (см. далее).

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

## Добавление таймкодов к аудио (через MFA)

Использует [MontrealCorpusTools](https://github.com/MontrealCorpusTools/) для принудительного выравнивания. 

Сначала нужно собрать образ со свежим MFA:
```
git clone https://github.com/MontrealCorpusTools/Montreal-Forced-Aligner.git
cd Montreal-Forced-Aligner
docker build --tag mfa .
```

Так как запустить через докер программный код, работающий через conda, оказалось не так то просто, то для экономиии трудозатрат пришлось разбить работу на 2 шага:

```
php82 timecodes_mfa.php syn bondarenko MODE_REPLACE 1
```

В результате первого шага будет предложено выполнить еще несколько команд. Выполняются эти команды в течение нескольких часов на одну версию озвучки. 
После выполнения предложенных команд, нужно обработать результаты MFA командой:

```
php82 timecodes_mfa.php syn bondarenko MODE_REPLACE 2
```

JSON с таймкодами будет сохранен в папке `audio`.
