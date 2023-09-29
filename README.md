<p align="center">
<a href="https://github.com/GalaxyGamesMC/Ovommand"><picture>
  <source media="(prefers-color-scheme: dark)" srcset="https://raw.githubusercontent.com/idumpster/image/main/ovommand/white/ovommand_white.svg" width="125" height="125">
  <source media="(prefers-color-scheme: light)" srcset="https://raw.githubusercontent.com/idumpster/image/main/ovommand/black/ovommand_black.svg" width="125" height="125">
  <img alt="ovo_logo" src="https://raw.githubusercontent.com/idumpster/image/main/ovommand/blue/ovommand_blue.svg" width="125" height="125">
</picture></a><br>
<b>a normal framework virion designed to parse command data for <a href="https://github.com/pmmp/PocketMine-MP">PocketMine-MP</a></b>
</p>

# Ovommand
TODO:
- [x] make reasonable enum that handle it value correctly (string -> value)
- [x] custom enum, parameters, enum-based parameters
- [ ] feature rich
- [ ] usage messages
- [x] result system (parser), not that good tho :l
- [ ] attribute supports (temp abandoned)


  Suggest:
- [x] make canParse and parse into one
- [ ] empty parameter functionality
- [ ] allow parameter to not provide data to the ingame auto-complete
- [ ] make overloadId global which will make the code shorter

[READ WIKI](https://github.com/GalaxyGamesMC/Ovommand/wiki)
<details> <summary>Show dumps</summary>

## A. Standard prototype
### 1. Commando structure
```php
class FirstSubCommand extends BaseSubCommand{
    public function prepare() {
        $this->addParameter(0, new IntParameter("coin"));
        $this->addParameter(1, new IntParameter("etc"));
    }
}

class TestCommand extend BaseCommand{
    public function prepare() {
        $this->addSubCommand(new FirstSubCommand(...));
    }
    // normal Commando command structure
}
```

## B. Attribute prototype:
Use Attributes to add metadata to Command, such as Overloads, Permissions, CommandEnum, etc
### 1. Parameter:
```php
#[CommandAttribute(
	null,
	'see',
	new Parameter('level', DefaultEnums::ONLINE_PLAYER)
)]
#[CommandAttribute(
	'level.cmd.op',
	'manager',
	'add'
)]
class AttributeCommand extend Command{
    // normal pmmp command structure
}
```
### 2. Nested parameter:
```php
#[CommandAttribute(
    null, //permission
    "hello", // descriptions
    new NestedParameter(parent: "hello", parameters: ["world", "me"])
)]
class AttributeCommand extend Command{
    // normal pmmp command structure
}
```
### 3. Other ideas
- More CommandAttribute type for adding more metadata
- Binding metadata to standard API

## C. THIS IS A SKID
1. [sky-min/CommandHelper](https://github.com/sky-min/CommandHelper)
2. [CortexPE/Commando](https://github.com/CortexPE/Commando)
3. [GalaxyGamesMC/libcommand](https://github.com/GalaxyGamesMC/libcommand)

<br><p align="center">
<strong>We are making a server!</strong><br>
<a href="https://thegalaxype.com">
<img alt="ovo_logo" src="https://avatars.githubusercontent.com/u/95261113?s=200&v=4" width="50" height="50">
</a><br>
<sub>Stay tuned for more!!</sub>
<a href="https://discord.gg/Ew7d7tBBPb"><sub>Discord!</sub></a>
</p>
</details>