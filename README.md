<p align="center">
<a href="https://github.com/GalaxyGamesMC/Ovommand"><picture>
  <source media="(prefers-color-scheme: dark)" srcset="https://raw.githubusercontent.com/idumpster/image/main/ovommand/svg/white/ovommand_title.svg" width="600" height="88">
  <source media="(prefers-color-scheme: light)" srcset="https://raw.githubusercontent.com/idumpster/image/main/ovommand/svg/black/ovommand_title.svg" width="600" height="88">
  <img alt="ovo_logo" src="https://raw.githubusercontent.com/idumpster/image/main/ovommand/svg/blue/ovommand_title.svg" width="600" height="88">
</picture></a><br>
<b>a feature-rich virion framework to handle commands for <a href="https://github.com/pmmp/PocketMine-MP">PocketMine-MP</a></b><br>
📔<a href="https://github.com/GalaxyGamesMC/Ovommand/wiki">Docs</a>⠀⠀🔌<a href="https://github.com/idumpster/OvoTest">Demo plugin</a> 
</p>

## 📖 Introduction
Ovommand is a command parsing and handling framework for PocketMine-MP. It made it easy for defining, registering, and executing commands with complex parameter structures, validation, and hierarchical organization.<br>
<blockquote>

  [!IMPORTANT]

  <strong>IMPORTANT</strong><br>
  Please note that this project is still at a very early stage and has not yet been released officially, and interfaces
  may be added or removed without notice. Please do not use Ovommand in a production!
<p align="center">
<img alt="ovo_warning" src="https://raw.githubusercontent.com/idumpster/image/main/ovommand/svg/yellow/short/ovommand_stupon.svg" width="65" height="45"><br>
<sup>This project is under construction....</sup>
</p>
</blockquote>

### Features
1. - [x] Custom command with rich customizability
2. - [x] Custom subcommand/parameters with command enum supports
3. - [ ] Parser for complex parameters with syntax checker
   - - [x] Position parameter (`~~ ~`, `12 -31 ~123`, etc) - Done?
   - - [ ] Target parameter (`@a`, `@s`, etc...) - Currently unstable
   - - [x] Float, Int, Text,... parameter
   - - [ ] Json parameter
4. - [x] Default enums support (player list, gamemode, boolean, etc)
5. - [x] Visible/Hidden aliases for subcommands.
6. - [x] Constraints for commands
7. - [x] Auto-generated usage for commands/subcommands
8. - [ ] Command namespace (from vanilla addon: `/plugin1:test`)
9. - [ ] Duplicate command name handle (adding counter to the command name)
10. - [ ] Attribute supports (temp abandoned)
11. - [ ] ?
### Requirement
- PHP 8.2 or higher
- [PMMP](https://github.com//pmmp/PocketMine-MP) 5.27.0 or higher
- [simple-packet-handler](https://github.com/Muqsit/SimplePacketHandler) 0.1.5 or higher
## Getting Started
## Contributing
Yes, please...

## Note
### A. Namespace system
1) If plugin A has `namespace: ns` set in `plugin.yml` then <br>
a) plugin A has `force namespace`
<hr>

## Issues:
1. - [ ] `$returnRaw` in `BaseResult` is confusing and useless?
2. - [ ] `isBlockPos` current do nothing in CoordinateResult
3. - [ ] Allow users to decide how the subcommand aliases are handled (multiple overloads vs packed enum)
4. - [ ] Command namespace system (as vanilla addon) and duplicate command name.








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