/**
 * @brief Executes the locate biome command to find the nearest biome of specified type
 * 
 * @param commandOrigin Pointer to the command origin (contains sender information)
 * @param output Pointer to the command output handler
 * 
 * This function:
 * 1. Gets the player's current position and level
 * 2. Attempts to locate the nearest specified biome
 * 3. If found, calculates distance and returns success message with coordinates
 * 4. If not found, returns error message
 */
void LocateCommand::_executeLocateBiome(
    const CommandOrigin* commandOrigin,
    CommandOutput* output) 
{
    // Get dimension and level from command origin
    __int64 dimension = commandOrigin->getDimension();
    if (!dimension) {
        return;
    }

    Level* level = commandOrigin->getLevel();
    if (!level) {
        return;
    }

    // Get player position
    BlockPos playerPos;
    commandOrigin->getPosition(&playerPos);

    // First attempt to locate biome
    BiomeSearchResult searchResult;
    LocateCommandUtil::locateNearbyBiome(&searchResult, &playerPos, dimension, this->biomeId);
    
    bool biomeFound = searchResult.found;
    
    // If not found in first attempt, try with extended range
    if (!biomeFound) {
        BlockPos extendedPos = {
            playerPos.x + 32,
            playerPos.y + 32,
            playerPos.z + 32
        };
        searchResult = *LocateCommandUtil::locateNearbyBiome(&searchResult, &extendedPos, dimension, this->biomeId);
        biomeFound = searchResult.found;
    }

    // Get biome name
    std::string biomeName;
    BiomeRegistry* registry = level->getBiomeRegistry();
    Biome* biome = registry->lookupById(this->biomeId);
    
    if (biome) {
        biomeName = biome->getName();
    } else {
        biomeName = "the biome";
    }

    // Handle search result
    if (biomeFound) {
        // Calculate straight-line distance
        int distance = static_cast<int>(mce::Math::sqrt(
            static_cast<float>(
                (searchResult.position.x - playerPos.x) * (searchResult.position.x - playerPos.x) +
                (searchResult.position.z - playerPos.z) * (searchResult.position.z - playerPos.z)
            )
        ));

        // Prepare success message parameters
        std::vector<CommandOutputParameter> params;
        params.push_back(CommandOutputParameter(biomeName));
        params.push_back(CommandOutputParameter(searchResult.position.x));
        params.push_back(CommandOutputParameter(searchResult.position.y));
        params.push_back(CommandOutputParameter(searchResult.position.z));
        params.push_back(CommandOutputParameter(distance));

        // Send success message
        output->success("commands.locate.biome.success", params);
    } else {
        // Prepare failure message parameters
        std::vector<CommandOutputParameter> params;
        params.push_back(CommandOutputParameter(biomeName));

        // Send error message
        output->error("commands.locate.biome.fail", params);
    }
}