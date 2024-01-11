<p align="center">
<a href="https://github.com/GalaxyGamesMC/Ovommand"><picture>
  <source media="(prefers-color-scheme: dark)" srcset="https://raw.githubusercontent.com/idumpster/image/main/ovommand/svg/white/ovommand.svg" width="125" height="125">
  <source media="(prefers-color-scheme: light)" srcset="https://raw.githubusercontent.com/idumpster/image/main/ovommand/svg/black/ovommand.svg" width="125" height="125">
  <img alt="ovo_logo" src="https://raw.githubusercontent.com/idumpster/image/main/ovommand/svg/blue/ovommand.svg" width="125" height="125">
</picture></a><br>
<b>a virion framework designed to parse command data for <a href="https://github.com/pmmp/PocketMine-MP">PocketMine-MP</a></b>
</p>

[READ WIKI](https://github.com/GalaxyGamesMC/Ovommand/wiki)

[DEMO PLUGIN](https://github.com/idumpster/OvoTest)

# Ovommand
NEW PROB:
- [ ] `$returnRaw` in `BaseResult` is confusing and useless?
- [ ] `isBlockPos` current do nothing in CoordinateResult
- [ ] Any plugins can edit the enums
- [ ] Enum's value can be illegally edit via outside packet
<details> <summary>Show useless dumps</summary>

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

</details>


:x:
:heavy_check_mark:
:white_check_mark:

|checked|unchecked|crossed|
|---|---|---|
|&check;|_|&cross;|
|&#x2611;|&#x2610;|&#x2612;|

<hr><p align="center">
<img alt="ovo_warning" src="https://raw.githubusercontent.com/idumpster/image/main/ovommand/svg/yellow/short/ovommand_stupon.svg" width="65" height="45"><br>
<sup>This project is under construction....</sup>
</p>