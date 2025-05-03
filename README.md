<p align="center">
<a href="https://github.com/GalaxyGamesMC/Ovommand"><picture>
  <source media="(prefers-color-scheme: dark)" srcset="https://raw.githubusercontent.com/idumpster/image/main/ovommand/svg/white/ovommand_title.svg" width="600" height="88">
  <source media="(prefers-color-scheme: light)" srcset="https://raw.githubusercontent.com/idumpster/image/main/ovommand/svg/black/ovommand_title.svg" width="600" height="88">
  <img alt="ovo_logo" src="https://raw.githubusercontent.com/idumpster/image/main/ovommand/svg/blue/ovommand_title.svg" width="600" height="88">
</picture></a><br>
<b>a feature-rich virion framework to handle commands for <a href="https://github.com/pmmp/PocketMine-MP">PocketMine-MP</a></b><br>
📔<a href="https://github.com/GalaxyGamesMC/Ovommand/wiki">Docs</a>⠀⠀🔌<a href="https://github.com/idumpster/OvoTest">Demo plugin</a> 
</p>

## About
Ovommand is a command parsing and handling framework for PocketMine-MP. It made it easy for defining, registering, and executing commands with complex parameter structures, validation, and hierarchical organization.<br>
### Features

## Getting Started




## Issues:
1. - [ ] `$returnRaw` in `BaseResult` is confusing and useless?
2. - [ ] `isBlockPos` current do nothing in CoordinateResult
3. - [ ] Allow users to decide how the subcommand aliases are handled (multiple overloads vs packed enum)
4. - [ ] Command namespace system (as vanilla addon) and duplicate command name.

<hr><p align="center">
<img alt="ovo_warning" src="https://raw.githubusercontent.com/idumpster/image/main/ovommand/svg/yellow/short/ovommand_stupon.svg" width="65" height="45"><br>
<sup>This project is under construction....</sup>
</p>








<details> <summary>Show useless dumps</summary>

Enums system:

|                                                                       | Soft Enum | Hard Enum |
|:----------------------------------------------------------------------|:---------:|:---------:|
| Can value be read by owner?                                           |    YES    |    YES    |
| Can value be read by other? (Public)                                  |    YES    |    YES    |
| Can value be read by other? (Private)                                 |    NO     |    NO     |
| Can value be written by owner? (Before server starts)                 |    YES    |    YES    |
| Can value be written by others? (Private)                             |    NO     |    NO     |
| Can value be written by others? (Before server starts, not Protected) |    YES    |    YES    |
| Can value be written by owner? (After server starts)                  |    YES    |    NO     |
| Can value be written by others? (After server starts, not Protected)  |    YES    |    NO     |
| Can value be written by others? (Before server starts, Protected)     |    NO     |    NO     |
| Can value be written by others? (After server starts, Protected)      |    NO     |    NO     |
| Can alias be written by owner? (Before server starts)                 |    YES    |    YES    |
| Can alias be written by owner? (After server starts)                  |    YES    |    NO     |
| Can alias be written by others? (Before server starts, not Protected) |    YES    |    YES    |
| Can alias be written by others? (Before server starts, Protected)     |    NO     |    NO     |
| Can alias be written by others? (After server starts, not Protected)  |    YES    |    NO     |
| Can alias be written by others? (After server starts, Protected)      |    NO     |    NO     |

TODO:
- [x] make reasonable enum that handle it value correctly (string -> value)
- [x] custom enum, parameters, enum-based parameters
- [x] feature rich
- [x] usage messages
- [x] result system (parser), not that good tho :l
- [ ] attribute supports (temp abandoned)
- [ ] SubCommand doesn't require perms if wanted
- [ ] ~~make syntax parser based on its string pos, not the string itself for the accuracy in catching broken syntax~~ spoiled
- [x] fix a bug where the parser cannot check the correct span leading to this to be valid: `/tp ~~~ a`, where a is not valid but the parser cannot know that because it don't expect that to be a case!
- [ ] fix broken SYNTAX_PRINT_VANILLA
- [ ] fix an issue where it failed to parse the parameter after position parameter that has less than the span! eg: ~~~ a, failed to parse a

Suggest:
- [x] make canParse and parse into one
- [x] empty parameter functionality
- [ ] allow parameter to not provide data to the ingame auto-complete
- [x] make overloadId global which will make the code shorter
- [ ] template? (temp abandoned)
- [ ] move part of Ovommand to BaseCommand
- [ ] do subCommand even need description?
- [x] more features to the syntax parser
- [ ] rename parsedId & matchedId in Results to rawParsedCount & parsedCount

Discuss:
- [ ] Default Enums should have its own register and a version checker!? (temp abandoned)
- [x] the problem with shared data is that if other plugins try to use other plugins enum... the enum might not exist due to plugin loading order!
- [x] Default enums can have duplicated values if the event called more than twice on different plugins!
- [ ] Merge onRun() and onSyntaxError()?
- [ ] Add supports for private enums and synced properties for soft enums

Self note:
- Soft enums cannot spread out its value using flag 1!
- Two enums, one soft and one hard could have a same name
- Enum name could be set to anything, not just ascii / UTF-8


:x:
:heavy_check_mark:
:white_check_mark:

|checked|unchecked|crossed|
|---|---|---|
|&check;|_|&cross;|
|&#x2611;|&#x2610;|&#x2612;|


</details>