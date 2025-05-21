 W/**
 * @brief Sets up the locate command with its subcommands and parameters
 * 
 * This function registers the "locate" command and its subcommands for locating structures and biomes
 * in the game world. It configures command parameters, validation, and version requirements.
 * 
 * @param registry Pointer to the CommandRegistry instance that will store the command configuration
 */
void LocateCommand::setup(CommandRegistry* registry) 
{
    // Buffer for storing temporary string data
    alignas(16) char stringBuffer[256];
    
    // ---- Structure subcommand registration ----
    
    // Initialize vectors for storing command options
    std::string structureCommandName = "LocateSubcommandStructure";
    std::vector<std::pair<std::string, ShowStoreOfferRedirectType>> structureOptions;
    
    // Register structure-related enum values
    CommandRegistry::addEnumValues<LocateSubcommand, CommandRegistry::DefaultIdConverter<LocateSubcommand>>(
        registry,
        &structureCommandName,
        &structureOptions
    );
    
    // ---- Biome subcommand registration ----
    
    // Initialize biome command options
    std::string biomeCommandName = "LocateSubcommandBiome";
    std::vector<std::pair<std::string, ShowStoreOfferRedirectType>> biomeOptions;
    
    // Register biome-related enum values
    CommandRegistry::addEnumValues<LocateSubcommand, CommandRegistry::DefaultIdConverter<LocateSubcommand>>(
        registry,
        &biomeCommandName,
        &biomeOptions
    );
    
    // ---- Main command registration ----
    
    // Register the base "locate" command
    std::string baseCommand = "locate";
    registry->registerCommand(
        &baseCommand,
        "commands.locate.description",
        CommandPermissionLevel::OPERATOR,  // Assuming 1 means operator level
        0,  // Command flags
        0   // Command type
    );
    
    // ---- Parameter setup ----
    
    // Create parameter for new chunks option
    auto newChunksParam = CommandParameterData(
        Bedrock::type_id<CommandRegistry, bool>(),
        &CommandRegistry::parse<bool>,
        "useNewChunksOnly",
        false,  // Optional parameter
        nullptr,
        0,
        CommandParameterOption::NONE
    );
    
    // Create parameter for structure type
    auto structureParam = CommandParameterData(
        Bedrock::type_id<CommandRegistry, StructureFeatureType>(),
        &CommandRegistry::parse<StructureFeatureType>,
        "structure",
        false,  // Required parameter
        nullptr,
        CommandParameterOption::ENUM
    );
    
    // Create parameter for biome type
    auto biomeParam = CommandParameterData(
        Bedrock::type_id<CommandRegistry, LocateCommandUtil::Biomes>(),
        &CommandRegistry::parse<LocateCommandUtil::Biomes>,
        "biome",
        false,  // Required parameter
        nullptr,
        CommandParameterOption::ENUM
    );
    
    // ---- Register command overloads ----
    
    // Register structure locate overload
    CommandVersion structureVersion(1, 21);  // Minimum version requirement
    registry->_registerOverload<LocateCommand>(
        structureVersion,
        newChunksParam,
        structureParam
    );
    
    // Register biome locate overload
    CommandVersion biomeVersion(21, 0x7FFFFFFF);  // Supported from version 21 onwards
    registry->_registerOverload<LocateCommand>(
        biomeVersion,
        biomeParam
    );
}