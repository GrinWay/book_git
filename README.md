# GIT

**Указатель на текущий коммит**

```
HEAD === @
```
<br>

**Обычный коммит и коммит слияния (который после merge) - разные по типу коммиты**

```plaintext
Потому что у обычного коммита есть только @^ === @~,
а у коммита слияния есть и @^, и @^2,
а возможно ещё и @^3, … @^n если при merge было указано несколько веток слияния,
но если есть намёк на конфликт, octopus стратегия при merge не рекомендуется.
```

> **Если проще, то коммит слияния - это когда parent > 1**

<br>

**MERGE-BASE для двух веток - один**

```
git merge-base f1 @ === git merge-base @ f1
```

> **А коммит слияния принадлежит только одной ветке, там где был сделан merge**

<br>

**master..f1 - Относительно master, что в f1**

```
Если речь идёт о "git cherry-pick master..f1"

То смысл:
Относительно master, какие коммиты в f1
То есть только коммиты из f1

Эквивалент: f1 ^master
Коммиты из f1, которых нет в master
```

```
Если речь идёт о "git diff master..f1"

То смысл:
Относительно master, что нового в f1
То есть учитывается как master, так и f1
```

> **Внутренне "rebase, log, cherry-pick" используют именно ..**  
> **X..Y - относительно X, все коммиты Y,**  
> **но кроме коммитов слияния (это уже особенность rebase)**

<br>

**master…f1 - Относительно MERGE-BASE, что в f1**

```
Если речь идёт о "git cherry-pick master...f1"

То смысл:
Относительно MERGE-BASE, какие коммиты в f1
То есть только коммиты из f1
```

```
Если речь идёт о "git diff master...f1"

То смысл:
Относительно MERGE-BASE, что нового в f1
```

> **Внутренне “merge, revert” используют именно …**

# **git diff - относительно X, что нового в Y**

| Command | Explanation                                                                                                                                                    |
| --- |----------------------------------------------------------------------------------------------------------------------------------------------------------------|
| git diff (уникальное сравнение) | Относительно IDX, что нового в LOCAL                                                                                                                           |
| git diff —cached   git diff —staged   git diff @ —cached | Относительно @, что нового в IDX                                                                                                                               |
| git diff @ (@ - это просто branch) | Относительно @, что нового в LOCAL                                                                                                                             |
| git diff master @   git diff master..@ | Относительно master, что нового в @                                                                                                                            |
| git diff master**…**@ | Относительно **MERGE-BASE** что нового в @                                                                                                                     |
| git diff master:file_1 @:file_2 | Сравнить 2 разных файла разных веток                                                                                                                           |
| git diff —no-index file_1 file_2 | Сравнить 2 разных файла вне системы GIT <br>  Эффект заметен только в GIT проекте (.git) <br>  Если это не GIT проект, то “git diff file_1 file_2” то же самое |
| ###> MERGE COMMIT ### | ###> MERGE COMMIT ###                                                                                                                                          |
| git diff @^2~ | Относительно theirs, 1 коммит назад, что нового в LOCAL                                                                                                        |

```
f1 ^master === master..f1

“Новое в f1, чего нет в master”
или эквивалентно
“Относительно master, что нового в f1”
```

```
@^ - каретка нужна только при работе с коммитами слияния

@^   === @~           - ours) первый родитель коммита
@^~  === @~2 === @~~  - ours) второй родитель коммита

НО ^ в первую очередь предназначена для коммитов слияния,
у кторых есть ours, theirs ветки

@^2          - theirs) первый родитель коммита
@^2~         - theirs) второй родитель коммита

@^3 - theirs2 - если это был "git merge f1 f2 --no-ff"
```

```
... === MERGE-BASE


@..f1 === ..f1       # если я на @

f1..@ === f1.. == f1 # если я на @
```

```
git diff @~ @ --word-diff=color # посмотреть различия именно слов
# эквивалент
git diff @~ @ --color-words
```
<br>

Права

**Git отслеживает только право на выполнение (x)**

> Так как на Windows нет отдельного права на выполнение,  
> когда файл можно читать, Windows подразумевает, что его можно и выполнять.
>
>
> Значит на Windows git отслеживает право на чтение

<br>

.gitignore

---

```
git add -f # force add ignoring .gitignore
```

> any_dir_or_file  
> any_dir/
>
> slash в любом месте, кроме конца, означает - от root  
> /root_dir  
> root/dir  
> root/dir/
>
> \*\*/\*.log === \*.log

```
Локальный .gitignore для проекта
vim .git/info/exclude
```

<br>

**check-ignore**

---

```
git check-ignore -v -- file # Если файл не под контролем git
                            # и в .gitignore найдено совпадение,
                            # то выведи строчку, ответственную за игнорирование
```

<br>

Файл .gitattributes (связывает типы файлов с аттрибутами git)

---

```
*.html diff=html # Это драйвер регулярных выражений для разных комманд git

                 # Для файлов .html использовать разделитель слов по правилам html
                  # чтобы git diff --color-words отображал правильно
                  # чтобы заголовок ханка был правильным

# Не показывать diff, потому что он бесполезный (бинарный файл)
*.svg -diff === *.svg diff=binary
# Только для работы diff=binary нужна настройка в .git/config
# .git/config
#   [diff "binary"]
#   binary = true

*.exe binary === *.exe -text -merge -diff
# -text  - Не выполнять CRLF -> LF при LOCAL -> .git
# -merge - При конфликтах оставляеть ours (теряются изменения)
# -diff  - Не показывать diff

/root/file merge=union # Просто объеденяй конфликтные строки, не начинай конфликтов

* eof=crlf  # .git  -> LOCAL   do   LF   -> CRLF
*.html text # LOCAL -> .git    do   CRLF -> LF
* text=auto # Сделать CRLF -> LF если содержимое файла тестовое
```

> Локальный .gitattributes для проекта  
> vim .git/info/attributes

<br>

**branch**

---

```
git branch --merged    # относительно ТЕКУЩЕЙ ВЕТКИ какие ветки вмёржены?

git branch --no-merged # относительно ТЕКУЩЕЙ ВЕТКИ какие ветки не вмёржены?
```

```
git branch newBranch commitId         # создаёт ветку на commitId

git branch existingBranch commitId -f # mv BRANCH
```

<br>

**merge** (cat .git/MERGE_HEAD - коммит, с которым происходит слияние)

---

```
git merge f1 --no-ff   # В текущей ветке создать коммит слияния с веткой f1
                       # --no-ff исключает перемотку

# Для простых правок можно использовать --squash
git merge f1 --squash  # diff @...f1 -> (LOCAL, IDX)
                       # не делает коммит
                       # Фактически, --squash не merge, потому что нет .git/MERGE_HEAD
                       # Предзаполняет commit comment

###> CONFLICT - это когда есть 3 версии IDX (:1:file, :2:file, :3:file) ###

git show :0:file # stage 0 - стабильной версии file IDX нету
git show :1:file # stage 1 - версия file для MERGE-BASE
git show :2:file # stage 2 - версия file для места куда делается merge (ours)
git show :3:file # stage 3 - версия file для MERGE_HEAD                (theirs)

git checkout --ours   -- file  # Установить своё  состояние у file
git checkout --theirs -- file  # Установить чужое состояние у file
git checkout --merge  -- file  # Установить merge состояние у file (с конфликтом)

git merge --abort    # Отменить   merge (фактически git reset --merge)
git merge --continue # Продолжить merge (фактически git commit)

###> Отменить merge ###
#  if IDX, resets LOCAL
#
#   Другими словами, merge добавляет файлы слияния в IDX
#   поэтому если отменить merge отменяются LOCAL, потому что они есть в IDX,
#   но если до отмены выполнить git add -A,
#   то git merge --abort сработает как git reset --hard

git merge --abort === git reset --merge
###< Отменить merge ###
```

> При конфликте  
> merge → (LOCAL, IDX)

> merge заканчивается коммитом

> PHPStorm по умолчанию показывает эти 3 версии содержимого в интерфейсе при конфликте  
> <br/>git checkout --merge --conflict=diff3 -- file  
> <br/>| 2-ours | 1-MERGE-BASE | 3-theirs |

```
git show коммита слияния покажет не все изменения,
для просмотра всех изменений: git diff @^ или git diff @^2

первая позиция - относительно ours
вторая позиция - относительно theirs

@@@ -1,4 -1,4 +1,4 @@@
- file1 <- относительно ours что нового в коммите          (убран "file1")
 -not!  <- относительно theirs что нового в коммите        (убран "not!")
++file  <- относительно ours и theirs что нового в коммите (добавлен "file")
  with
  interesting
  content
```

```
Крайне не рекомендуется делать множественный merge:
git merge f1 f2 # это стратегия octopus

Потому что если будут конфликты, то при их разрешении:
1) добавляются непонятные идентификаторы >>>>>>> .merge_file_2M0JaC
2) в ours подмешиваются изменения с f1 (не только master, но и f1 изменения)
   Теряется информацию откуда какие изменения в ours

Как минимум merge станоситься сложнее, как максимум можно знатно запутаться

octopus strategy можно использовать если есть 100% гарантия отсутствия конфликтов,
иначе будет тяжко, а нам ведь не хочется чтобы было тяжко
```

```
Коммит слияния есть только в той ветке, в которой был merge
```

> git merge --no-commit # не делать коммит

```
# BEST PRACTICE
--no-ff # Не делать перемотку
```

> Слить другую ветку в папку (как библиотека)  
> git merge --Xsubtree=f1 --allow-unrelated-histories f1

<br>

**show**

---

```
git show :/"commentWord" # Найти первый коммит по слову в комментарии
                         # !ГЛОБАЛЬНО! - СРЕДИ ВСЕХ КОММИТОВ (любая ветка)

git show master:/"commentWord" # Найти первый коммит по слову в комментарии
                               # среди коммитов ветки master
```

```
git show @~:file         # Посмотреть содержимое файла определённого коммита

# Эквивалетные записи: просмотр IDX
git show :file           # IDX: stage 0
git show :0:file         # IDX: stage 0

git show --first-parent  # если это коммит слияния,
                         # то относительно ours что нового в коммите

# Проверка, коммит слияния ли?
git show @^2
```

```
Вместо "git diff @~ @"
можно просто "git show"

Так как "git show" показывает изменения коммита, а "git diff" сравнение коммитов
```

<br>

**checkout**

---

```
git checkout -- file    # IDX -> LOCAL

git checkout @ -- file  # @ -> (LOCAL, IDX)
git reset    @ -- file  # @ -> (IDX)

git checkout -          # Сокращение для переключения не предыдующую ветку
git checkout @{-1}       # Переключиться на предыдующую ветку
                         # берёт 1-ю запись "checkout" из reflog

git checkout -f === git checkout @ -f   # @ -> (LOCAL, IDX)
```

```
# mv BRANCH (IDX не трогается)

git branch f1 @~ -f                    # mv BRANCH
git checkout -B f1 @~                  # mv BRANCH + checkout

git checkout f1 && git reset @~ --soft # более явно, предпочтительнее
```

<br>

**add** (git add - добавляет в IDX)

---

```
# Экзотика

git add . -p # Интерактивное добавление в IDX
             # если в одном файле изменения разных тем
```

```
git add -A # как . но от корня проекта
```

<br>

**reset**

---

```
git reset @ --soft                        # mv BRANCH

git reset @ --mixed                       # mv BRANCH, reset (IDX) | default

git reset --hard === git reset @ --hard   # mv BRANCH, reset (IDX, LOCAL)

git reset --keep # Безопасный --hard (сделает reset если нет конфликтов)
                 # reset (IDX, LOCAL)
```

> Пишет в .git/ORIG_HEAD перед reset

<br>

**rebase** (пишет в .git/ORIG_HEAD перед перебазированием)

---

```
rebase лучше запускать:
1) в интерактивном режиме (-i), чтобы видеть использованные коммиты
2) --rebase-merges если в ветке, которую я хочу перебазировать, есть merges сторонних веток, то использовать именно коммиты слияния, а не коммиты сторонних веток

# Best practice:
 # -i              # Включает interactive mode
 # --rebase-merges # Перебазировать коммиты слияния если такие есть
 # --edit-todo     # Посмотреть что осталось по списку

git rebase master -i --rebase-merges
```

```
Если у ветки, которую хочешь перебазировать есть коммиты слияния от других веток,
то rebase, по умолчанию, вместо копирования коммитов слияния будет копировать
коммиты из другой ветки

флаг --rebase-merges - ЛУЧШЕ ПОДХОДИТ ОЖИДАЕМОМУ ПОВЕДЕНИЮ
он заставляет rebase копировать именно коммиты слияния других веток к переносимой ветке.
Если такие merge к переносимой ветке вообще были.
```

> ```
> # Текущая ветка итак базируется от currentId
> # Я хочу перебазировать её на саму себя для чистой истории
> # Например если был revert в master чтобы merge master снова применился
> 
> git rebase currentId --no-ff # перебазировать заново
> ```

```
git checkout f1 && git rebase master

# master..f1 === f1 ^master
# Относительно master, все коммиты f1, кроме коммитов слияния, перебазировать от master
# изменения от коммитов слияния других веток в f1 тоже будут, но в виде отдельных коммитов, а не одним коммитом слияния
```

```
git checkout f1 && git rebase --onto master release

# release..f1 === f1 ^release
# Относительно release, все коммиты f1, кроме коммитов слияния, перебазировать от master
```

```
git checkout f1 && git rebase master --rebase-merges

# master..f1 === f1 ^master
# Относительно master, все коммиты f1 перебазировать от master

# Теперь если есть коммиты слияния от других веток в f1
# копироваться будут именно коммиты слияния, а не предшествующие им коммиты до merge
```

```
git checkout f1   # от master
git rebase master # перебазируй f1 от текущего состояния master
                  # НАЧИНАЯ ОТ master..f1 (относительно master, коммиты f1)


####> CONFLICT ####
git rebase --abort  # mv HEAD где была до rebase

git rebase --quit   # Оставляет HEAD на последнем скопированном коммите (detached HEAD)
```

```
git checkout f1                  # от release
git rebase --onto master release # перебазируй f1 от текущего состояния master
                                 # НАЧИНАЯ ОТ release..f1
                                 # (относительно release, коммиты f1)

# Когда нужно перебазировать "относительно другой ветки" используй "--onto":
 # git rebase --onto master release
 # release..f1 === f1 ^release
```

> Если хочется вернуть f1 на прежнее состояние, до перебазирования  
> <br/>Можно сразу после rebase сделать git reset ORIG_HEAD --hard

<br>

**commit**

---

```
git commit --amend === git commit -c @ # Замена коммита
                                       # берёт описание и автора из @

                                       # -C большая - не открыла бы редактор
                                       # --reset-author не брать автора из @

git commit --amend --reset-author === git reset @~ --soft && git commit
```

```
# Стоит выполнить rebase -i и вместо пачки коммитов он схлопнется
git commit --squash=@~ + git rebase -i --autosquash @~3 # rebase применит squash

# Стоит выполнить rebase -i и вместо пачки коммитов он схлопнется
# Не сохранять текущий комментарий (fixup)
git commit --fixup=@~  + git rebase -i --autosquash @~3 # rebase применит fixup
```

<br>

**cherry-pick** (copy commit)

---

```
git cherry-pick f1 -n      # no commit | diff -> (LOCAL, IDX)

git cherry-pick f1 -x      # добавляет комментарий в коммит о cherry picking

git cherry-pick master..f1 # скопировать все коммиты f1, которых нет в master


git cherry-pick @ -m 1     # "-m 1" для коммита слияния
                           # Означает: относительно ours, что нового в @

###> CONFLICT ###
git cherry-pick --quit     # Остановиться на текущем состоянии, не продолжать копировать
```

<br>

**revert** (Делает коммиты отмены. Работа revert основана на cherry-pick)

---

```
-m 1         # ОТНОСИТЕЛЬНО ВЕТКИ, КУДА Я ДЕЛАЛ MERGE (ours)
git revert @ # ОТМЕНИ НОВОЕ

# Для коммитов слияния нужно указывать относительно чего "-m 1" === ours
# Относительно ours отмени новое от @
git revert @ -m 1
```

```
git revert @~3... # Относительно MERGE-BASE (@~3, @)
                  # сделает 3 commit revert (revert, revert, revert)

git revert @ === git revert @~...
```

> rebase предпочтительнее повторного коммита отмены revert  
> <br/>Но rebase можно применять только в том случае, если изменения локальны,  
> а не в удалённом репозитории

```
Есть 2 способа сделать повторный merge когда был revert:
1) git logg master...f1 --boundary # Относительно MERGE-BASE коммиты master и f1
                                   # Посмотреть самое начало master (foundMasterOrigin)
                                   # когда f1 только только был создан от master
   
   # либо
   # можно прям напрямую перебазировать f1 на master
   # относительно MERGE-BASE (foundMasterOrigin, f1)
   git checkout f1 && \
   git rebase --onto master foundMasterOrigin && \ # <- получаем последние правки master
   git checkout master && \
   git merge f1 --no-ff

   # либо
   # оставить f1 на своём base, просто заново
   # прокопировать когда он только только был создан от master
   git checkout f1 && \
   git rebase foundMasterOrigin --no-ff && \ # <- не получаем последние правки master
   git checkout master && \
   git merge f1 --no-ff

2) git checkout master && git revert revertCommit && git merge f1


1-й вариант "git rebase" предпочтительнее,
но это возможно только если соблюдены 2 момента:
  1) f1 не была в remote
     то есть никто другой не мог себе скачать себе эту ветку.
  2) Не было перемоток, только реальные слияния (git merge f1 --no-ff)

2-й вариант до самого merge отмена отмены - работает всегда
```

<br>

**reflog** (не отправляются вместе с коммитами, чисто локальное хранилище “cat .git/logs/HEAD“)

---

```
git reflog === git reflog show === git log -g --oneline
# -g переключает git log в режим отображения "где был..." в данном случе @
```

```
git reflog master --date=iso -1 # 1 перемещение по ветке master
                                 # до 90 дней назад для коммитов в ветке
                                 # до 30 дней назад для detached коммитов
```

```
Ссылки на reflog (работают локально)

HEAD@{1}
master@{2}
f1@{4}
```

```
git reflog expire --expire=now --all # Очистить reflog
git gc --prune=now # обычно после этого чистят недостижимые коммиты git
```

<br>

**log** (поиск по _comment_, _diff_, _file…_)

---

```
# show DIFF
git log -p


git log                # выводит коммиты текущей ветки,
                       # НО СЛЕДУЕТ ПО ВСЕМ MERGE PARENTS 
git log --first-parent # следуй только по первому parent (чистая линия)


git log --all      # коммиты всех веток

git log master m1  # коммиты этих двух веток
                   # --graph

# log by COMMENT RegEx
git log --grep 'comment text' --grep 'OR this text (by default)'
git log --grep 'comment text' --grep 'AND this text' --all-match
git log --grep 'RegEx' -P -i  # -i - case insensitive
                              # -P - perl regex (grep.patternType)

# log by DIFF RegEx
git log -G 'RegEx'            # поиск по diff


# file changing
git log master -- file # коммиты master, которые меняли file
                       # --follow - если нужны прежне переименованные файлы
# log by line  range
git log -L 10,20:file  # коммиты, которые меняли файл file в диопазоне с 10 по 20 строчки
# log by RegEx range
git log -L '/RegExStart/','/RegExEnd/':file # указать диопазон через RegEx


# log by AUTHOR RegEx
git log --author="RegEx" # коммиты определённого автора

# log by COMMITTER RegEx
git log --committer="RegEx" # коммиты определённого коммитера


# эквивалентно
git log master..f1 # относительно master, коммиты в f1
git log f1 ^master # коммиты ветки f1, не master

git log master...f1 --boundary # комиты двух веток от MERGE-BASE

                               # ДЛЯ DIFF ЗНАЧЕНИЕ ... НЕМНОГО ДРУГОЕ
                               # ОТНОСИТЕЛЬНО MERGE-BASE, ЧТО НОВОГО В f1
                               # то есть diff вывел бы исключительно новое в f1,
                               # а log выводит коммиты двух веток (f1 и master)

# log by Date period
git log --before '3 months'
git log --after  '1 hour'
```

> **git log master…f1 --cherry-pick --oneline --graph --boundary**
>
> **\# cherry-pick - Удаляет эквивалентные коммиты**
>
> **\# cherry-mark - Помечает “=” эквивалентные коммиты**

<br>

**blame**

---

```
git blame -L 1,2 --date=short -- file # посмотреть author с 1 по 2 линии файла file
```

<br>

**tag**

---

```
git tag --contains commitId # Убедиться, что на commitId повешен тег

git tag --contains @        # Убедиться, что на @ повешен тег
git tag --contains
```

```
git tag -n10      # У каждого тега отобразить 10 строк комментария
```

```
git tag -l 'v1*'  # -l - Pattern по tag name
```

```
git tag -d v1.0.0 # Удалить тег
```

```
git tag -am 'Description' # Аннотированный тег
```

```
git describe commitId # покажи ближайший tag
# Покажет просто тег если он прям на commitId

# или TAG-N-gSHORT_COMMIT_ID (v1.0.2-1-gc67a7b6)
# N - на сколько коммитов назад тег
```

<br>

**archive**

---

```
git archive -o ~/path/to/$(git describe @).zip @ # Архивировать HEAD
```

<br>

**gc** (Сборщик мусора удаляет всё, что без branch, tag, reflog)

---

```
git gc --prune=now
```

<br>

**rerere** (Хранит сохранённые разрешения конфликтов в .git/rr-cache/)

---

```
# Переключитьс на конфликт
git checkout --merge -- file
# Забыть текущее разрешение конфликта
git rerere forget -- file
```

<br>

**clean** (Очень опасная команда)

---

```
git clean -fd  # Удалить все файлы и директории (d), которыми НЕ управляет git
               # (x - даже те файлы, что игнорирует .gitignore)
```

<br>

settings

---

```
git config --global --list
```

```
git config --global core.editor vim
```

```
git config --global merge.conflictStyle diff3
```

```
git config --global core.excludesFile ~/.gitignore
```

```
git config --global core.attributesFile ~/.gitattributes
```

```
git config --global rebase.missingCommitsCheck warn
```

```
git config --global rebase.autoSquash true
```

```
git config --global merge.ff false
```

```
git config --global grep.patternType perl
```

```
git config --global rerere.enabled true
```

```
git config --global pretty.my '%C(auto)%h%Creset %C(bold blue)%an%Creset <%ae> %C(dim white)(%ar)%Creset%n%C(yellow)%d%Creset%n%Creset%s%n%C(dim)%b%Creset%n%C(green)Commit:%Creset %H%n%C(green)AuthorDate:%Creset %ad%n%C(green)CommitDate:%Creset %cd%n%C(green)Refs:%Creset %D%n'

git config --global format.pretty my
```

```
# Лучшая насройка git diff:

sudo apt install git-delta -y

git config --global core.pager delta
git config --global delta.side-by-side true
git config --global delta.line-numbers true
git config --global delta.syntax-theme Dracula
```

<br>

**ORIG_HEAD === cat .git/ORIG_HEAD**

---

```
Git сохраняет предыдущий коммит перед
- merge
- reset
- rebase
...
И другие команды, которые умеют откатываться (--abort)
```

### ALIAS

---

git logg

```bash
alias.logg=log --color --graph --pretty=format:'%Cred%h%Creset -%C(yellow)%d%Creset %s %Cgreen(%cr)%C(bold blue)<%an %ae>%Creset' --abbrev-commit
```