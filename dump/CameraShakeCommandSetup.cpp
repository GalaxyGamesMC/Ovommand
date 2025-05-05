/**
 * Set up and register the `camerashake` command. This command allows players or operators
 * to manipulate camera shake effects in the game by defining parameters like intensity, 
 * duration, type, and target players.
 *
 * @param registry Pointer to the `CommandRegistry` instance where the commands are registered.
 */
void __fastcall CameraShakeCommand::setup(struct CommandRegistry *registry)
{
    // Simulated vector initialization for string storage
    __m128i localStringData;
    std::pair<std::string, Rotation> cameraShakeActionPairs[2];
    std::pair<std::string, float> cameraShakeParameters[3];
    std::pair<std::string, CommandSelector<Player>> playerParameter;

    struct CommandRegistry::Signature *commandSignature = nullptr;

    // Step 1: Initialize threading (if needed)
    if (threadLocalMeta > *(_DWORD *)(*(_QWORD *)NtCurrentTeb()->ThreadLocalStoragePointer + 48LL))
    {
        Init_thread_header(&threadLocalMeta);
        if (threadLocalMeta == -1)
        {
            xmmThreadLocalMetadata = (__int128)_mm_load_si128((const __m128i *)&_xmm);
            threadKeyLocalValue = 0;
            std::string::assign(&threadKeyLocalValue, &unk_14198F1E0, 0LL);
            atexit(CameraShakeCommand::setup_::_2_::_dynamic_atexit_destructor_for__label_19__);
            Init_thread_footer(&threadLocalMeta);
        }
    }

    // Step 2: Register ENUM - "CameraShakeAction"
    localStringData = _mm_load_si128((const __m128i *)&_xmm);
    cameraShakeActionPairs[0] = std::make_pair("add", Rotation::ADD);
    cameraShakeActionPairs[1] = std::make_pair("replace", Rotation::REPLACE);
    CommandRegistry::addEnumValues<enum CameraShakeCommand::CameraShakeAction>(
        registry, "CameraShakeAction", cameraShakeActionPairs);

    // Step 3: Register ENUM - "CameraShakeType"
    std::pair<std::string, CameraShakeType> cameraShakeTypePairs[2] = {
        std::make_pair("positional", CameraShakeType::POSITIONAL),
        std::make_pair("rotational", CameraShakeType::ROTATIONAL)
    };
    CommandRegistry::addEnumValues<enum CameraShakeType>(
        registry, "CameraShakeType", cameraShakeTypePairs);

    // Step 4: Register COMMAND - "camerashake"
    CommandRegistry::registerCommand(
        registry, 
        "camerashake",                         // Command name
        "commands.screenshake.description",    // Command description key
        CommandRegistry::CommandPermissions::OPERATOR,  // Operator-only
        0, 0  // Additional flags
    );

    // Step 5: Register Command Parameters
    std::string shakeTypeParam = "shakeType";
    CommandRegistry::registerParameter<enum CameraShakeType>(
        &shakeTypeParam, "CameraShakeType", CommandRegistry::BindingFlags::OPTIONAL);

    std::string intensityParam = "intensity";
    CommandRegistry::registerParameter<float>(
        &intensityParam, "commands.camerashake.intensity.description", CommandRegistry::BindingFlags::OPTIONAL);

    std::string durationParam = "seconds";
    CommandRegistry::registerParameter<float>(
        &durationParam, "commands.camerashake.seconds.description", CommandRegistry::BindingFlags::OPTIONAL);

    std::string targetPlayerParam = "player";
    CommandRegistry::registerParameter<CommandSelector<Player>>(
        &targetPlayerParam, "commands.camerashake.player.description", CommandRegistry::BindingFlags::OPTIONAL);

    // Step 6: Find Registered Command and Build Overload
    commandSignature = CommandRegistry::findCommand(registry, "camerashake");
    if (commandSignature)
    {
        // Overload signature
        auto overloadData = std::unique_ptr<CameraShakeCommand>();

        if (commandSignature->overloadCount() < 5)
        {
            CommandRegistry::buildOverload<CommandParameterData>(
                commandSignature, overloadData, shakeTypeParam, intensityParam, durationParam, targetPlayerParam);
        }

        // Finalize and register the overload
        CommandRegistry::registerOverloadInternal(registry, commandSignature, overloadData);
    }
}