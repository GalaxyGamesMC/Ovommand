<?php
declare(strict_types=1);

namespace galaxygames\ovommand\parameter\parse;

// TODO: should I?

final class Targets{
	public const TYPE_PLAYER_NAME = 0;
	public const TYPE_ALL_ENTITIES = 1; // @e: selects all entities (players, cows, dropped items, etc.)
	public const TYPE_ALL_PLAYERS = 2; // @a: selects all online players, alive or not
	public const TYPE_NEAREST_PLAYERS = 3; //@p: selects the single closest living player unless the execution origin
	// is changed with the x, y, and z selector parameters. If the executor was a command block, the player closest to
	// the command block would be selected since the command block's coordinates are the execution origin.
	public const TYPE_RANDOM_PLAYERS = 4; // @r: selects one random living player unless the type parameter is specified.

	/*
	https://learn.microsoft.com/en-us/minecraft/creator/documents/targetselectors
	https://wiki.bedrock.dev/commands/selectors.html

	https://github.com/presentkim-pm/TargetSelector/tree/main
	https://poggit.pmmp.io/p/PlayerSelectors/1.0.7
	 */
}
