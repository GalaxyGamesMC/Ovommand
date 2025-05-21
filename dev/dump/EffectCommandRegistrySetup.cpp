/**
 * Sets up the Effect command with its parameters and overloads
 * @param registry The command registry to register the command with
 */
void __fastcall EffectCommand::setup(CommandRegistry* registry) {
    // Register the Effect command modes (clear, etc)
    registerEffectModes(registry);
    
    // Register available effects
    registerEffectTypes(registry);
    
    // Register the base command
    registerBaseCommand(registry);
    
    // Register command overloads
    registerCommandOverloads(registry);
}

/**
 * Registers the effect command modes (clear, etc)
 */
private void registerEffectModes(CommandRegistry* registry) {
    std::vector<std::pair<std::string, EffectCommand::Mode>> modes;
    modes.emplace_back("ClearEffects", EffectCommand::Mode::Clear);
    
    registry->addEnumValues<EffectCommand::Mode, CommandRegistry::DefaultIdConverter<EffectCommand::Mode>>(
        "Effect",
        modes
    );
}

/**
 * Registers all available mob effects
 */
private void registerEffectTypes(CommandRegistry* registry) {
    std::vector<std::pair<std::string, MobEffect*>> effects;
    
    // Iterate through all mob effects
    for (auto* effect : MobEffect::mMobEffects) {
        if (effect && effect != MobEffect::EMPTY_EFFECT) {
            auto serverType = PropertiesSettings::getServerType();
            effects.emplace_back(effect->getName(), effect);
        }
    }
    
    registry->addEnumValues<MobEffect const*, CommandRegistry::DefaultIdConverter<MobEffect const*>>(
        "Effect",
        effects
    );
}

/**
 * Registers the base effect command
 */
private void registerBaseCommand(CommandRegistry* registry) {
    registry->registerCommand(
        "effect",
        "commands.effect.description",
        CommandFlag::Normal,
        CommandPermissionLevel::GameMasters
    );
}

/**
 * Registers all command parameter overloads
 */
private void registerCommandOverloads(CommandRegistry* registry) {
    // Register clear effect overload
    registerClearEffectOverload(registry);
    
    // Register apply effect overload
    registerApplyEffectOverload(registry);
}

/**
 * Registers the overload for clearing effects
 */
private void registerClearEffectOverload(CommandRegistry* registry) {
    auto modeParam = CommandParameterData::create<EffectCommand::Mode>(
        "clear",
        CommandParameterDataFlags::NONE,
        nullptr,
        nullptr
    );
    
    auto targetParam = CommandParameterData::create<CommandSelector<Actor>>(
        "player",
        CommandParameterDataFlags::NONE,
        nullptr,
        nullptr
    );
    
    registry->registerOverload<EffectCommand>(
        CommandVersion(1, 0x7FFFFFFF),
        modeParam,
        targetParam
    );
}

/**
 * Registers the overload for applying effects
 */
private void registerApplyEffectOverload(CommandRegistry* registry) {
    auto params = {
        CommandParameterData::create<MobEffect const*>(
            "effect",
            CommandParameterDataFlags::NONE,
            nullptr,
            nullptr
        ),
        CommandParameterData::create<CommandSelector<Actor>>(
            "player",
            CommandParameterDataFlags::NONE,
            nullptr,
            nullptr
        ),
        CommandParameterData::create<int>(
            "seconds",
            CommandParameterDataFlags::OPTIONAL,
            nullptr,
            nullptr
        ),
        CommandParameterData::create<int>(
            "amplifier", 
            CommandParameterDataFlags::OPTIONAL,
            nullptr,
            nullptr
        ),
        CommandParameterData::create<bool>(
            "hideParticles",
            CommandParameterDataFlags::OPTIONAL,
            nullptr,
            nullptr
        )
    };
    
    registry->registerOverload<EffectCommand>(
        CommandVersion(1, 0x7FFFFFFF),
        params
    );
}