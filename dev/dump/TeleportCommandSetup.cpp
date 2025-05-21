// Configures the teleport command in the command registry, defining its syntax and parameters.
// Parameters:
// - registry: Pointer to the CommandRegistry where the teleport command will be registered.
void __fastcall TeleportCommand::setup(CommandRegistry* registry)
{
    // Local variables for command configuration and parameter storage
    std::string commandNameBuffer[32]; // Buffer for command name string
    int commandFlag = 0;              // Flag for command properties
    __int64 aliasBuffer[4];           // Buffer for command alias
    __int64 facingEnumBuffer[4];      // Buffer for facing enum values
    unsigned __int64 facingEnumLength = 15ULL; // Length of facing enum string
    __int64 facingEnumSize = 14LL;    // Size of facing enum data
    _BYTE facingVectorBuffer[24];     // Buffer for facing vector data
    __int64 facingVectorSize = 0LL;   // Size of facing vector
    __int128* tempStringPtr = nullptr; // Temporary string pointer
    _QWORD tempPairBuffer[2];         // Buffer for pair data
    _BYTE tempByteBuffer[8];          // Temporary byte buffer
    __m128i tempVector128[2];         // Temporary 128-bit vector buffer
    __int64 paramBuffer1[2];          // Buffer for first parameter data
    __int64 paramBuffer2[2];          // Buffer for second parameter data
    __int64 paramBuffer3[2];          // Buffer for third parameter data
    __int64 paramBuffer4[2];          // Buffer for fourth parameter data
    __int64 paramBuffer5[2];          // Buffer for fifth parameter data
    _QWORD tempCommandVersion[2];      // Buffer for command version
    __m128i tempVector128_2[2];       // Second temporary 128-bit vector buffer
    __m128i tempVector128_3[2];       // Third temporary 128-bit vector buffer
    __m128i tempVector128_4[2];       // Fourth temporary 128-bit vector buffer
    __m128i tempVector128_5[2];       // Fifth temporary 128-bit vector buffer
    _QWORD tempPairBuffer2[3];        // Second buffer for pair data
    unsigned __int64 tempPairLength = 0ULL; // Length of pair buffer
    _OWORD stringArray[2];            // Array for string storage
    char tempChar = 0;                // Temporary character storage

    // Initialize string array for command name
    memset(stringArray, 0, sizeof(stringArray));
    std::string::_Construct<1, char const*>(stringArray);
    commandFlag = 0;

    // Set up command name pair
    tempPairBuffer[0] = stringArray;
    tempPairBuffer[1] = &tempChar;

    // Construct vector of facing conditions
    std::vector<std::pair<std::string, enum NewExecuteCommand::ExecuteChainedSubcommand::ConditionSubcommand>>::vector<
        std::pair<std::string, enum NewExecuteCommand::ExecuteChainedSubcommand::ConditionSubcommand>>(
        facingVectorBuffer, tempPairBuffer, tempByteBuffer);

    // Initialize facing enum data
    facingEnumSize = 14LL;
    facingEnumLength = 15LL;
    qmemcpy(&facingEnumBuffer, "TeleportFaci", 12);
    HIDWORD(facingEnumBuffer) = *(unsigned __int16*)"ng";

    // Register facing enum values with the command registry
    CommandRegistry::addEnumValues<enum RotationCommandUtils::FacingResult, CommandRegistry::DefaultIdConverter<enum RotationCommandUtils::FacingResult>>(
        registry, &facingEnumBuffer, facingVectorBuffer);

    // Clean up facing enum buffer if necessary
    if (facingEnumLength >= 0x10)
    {
        __int64 bufferPtr = facingEnumBuffer;
        if (facingEnumLength + 1 >= 0x1000)
        {
            bufferPtr = *(_QWORD*)(facingEnumBuffer - 8);
            if ((unsigned __int64)(facingEnumBuffer - bufferPtr - 8) > 0x1F)
                _invalid_parameter_noinfo_noreturn();
        }
        std::_Return_temporary_buffer<unsigned int>(bufferPtr);
    }

    // Reset facing enum data
    facingEnumSize = 0LL;
    facingEnumLength = 15LL;
    LOBYTE(facingEnumBuffer) = 0;

    // Clean up facing vector if it exists
    if (*(_QWORD*)facingVectorBuffer)
    {
        std::_Destroy_range<std::allocator<std::pair<std::string, enum VolumeAreaCommand::Mode>>>(
            *(_QWORD*)facingVectorBuffer, *(_QWORD*)&facingVectorBuffer[8], facingVectorBuffer);
        __int64 vectorPtr = *(_QWORD*)facingVectorBuffer;
        if ((unsigned __int64)(40 * ((*(_QWORD*)&facingVectorBuffer[16] - *(_QWORD*)facingVectorBuffer) / 40LL)) >= 0x1000)
        {
            vectorPtr = *(_QWORD*)(*(_QWORD*)facingVectorBuffer - 8LL);
            if ((unsigned __int64)(*(_QWORD*)facingVectorBuffer - vectorPtr - 8) > 0x1F)
                _invalid_parameter_noinfo_noreturn();
        }
        std::_Return_temporary_buffer<unsigned int>(vectorPtr);
        memset(facingVectorBuffer, 0, sizeof(facingVectorBuffer));
    }

    // Clean up string array
    `eh vector destructor iterator`(
        stringArray, 0x28uLL, 1uLL,
        std::pair<std::string, enum LootCommand::Target>::~pair<std::string, enum LootCommand::Target>);

    // Set up teleport command name
    facingEnumSize = 8LL;
    facingEnumLength = 15LL;
    facingEnumBuffer = 0x74726F70656C6574uLL; // "teleport"
    LOBYTE(commandFlag) = 1;

    // Register the teleport command
    CommandRegistry::registerCommand(registry, &facingEnumBuffer, "commands.tp.description", commandFlag, 0, 0);

    // Clean up command name buffer
    if (facingEnumLength >= 0x10)
    {
        __int64 bufferPtr = facingEnumBuffer;
        if (facingEnumLength + 1 >= 0x1000)
        {
            bufferPtr = *(_QWORD*)(facingEnumBuffer - 8);
            if ((unsigned __int64)(facingEnumBuffer - bufferPtr - 8) > 0x1F)
                _invalid_parameter_noinfo_noreturn();
        }
        std::_Return_temporary_buffer<unsigned int>(bufferPtr);
    }

    // Set up alias for the teleport command
    tempStringPtr = &facingEnumBuffer;
    facingEnumBuffer = 0LL;
    facingEnumSize = 0LL;
    facingEnumLength = 0LL;
    std::string::_Construct<1, char const*>(&facingEnumBuffer);
    memset(facingVectorBuffer, 0, sizeof(facingVectorBuffer));
    facingVectorSize = 0LL;
    std::string::_Construct<1, char const*>(facingVectorBuffer);
    CommandRegistry::registerAlias(registry, facingVectorBuffer, &facingEnumBuffer);

    // Define parameter for checking blocks
    int typeIdBool = Bedrock::type_id<CommandRegistry, bool>(tempByteBuffer);
    CommandParameterData::CommandParameterData(
        (unsigned int)paramBuffer1, typeIdBool, (unsigned int)CommandRegistry::parse<bool>,
        (unsigned int)"checkForBlocks", 0, 0LL, 0LL, 751, 1, -1);

    // Define parameter for destination coordinates
    int typeIdPosition = Bedrock::type_id<CommandRegistry, CommandPositionFloat>(&tempStringPtr);
    CommandParameterData::CommandParameterData(
        (unsigned int)paramBuffer2, typeIdPosition, (unsigned int)CommandRegistry::parse<CommandPositionFloat>,
        (unsigned int)"destination", 0, 0LL, 0LL, 496, 0, 748);

    // Register command overload with destination and checkForBlocks
    CommandVersion::CommandVersion((CommandVersion*)tempCommandVersion, 1, 0x7FFFFFFF);
    CommandRegistry::_registerOverload<TeleportCommand, CommandParameterData, CommandParameterData>(
        registry, (__int64)paramBuffer1);

    // Clean up temporary buffers
    if (tempVector128_5[0].m128i_i64[1] >= 0x10uLL)
    {
        __int64 bufferPtr = tempPairBuffer2[0];
        if ((unsigned __int64)(tempVector128_5[0].m128i_i64[1] + 1) >= 0x1000)
        {
            bufferPtr = *(_QWORD*)(tempPairBuffer2[0] - 8LL);
            if ((unsigned __int64)(tempPairBuffer2[0] - bufferPtr - 8) > 0x1F)
                _invalid_parameter_noinfo_noreturn();
        }
        std::_Return_temporary_buffer<unsigned int>(bufferPtr);
    }
    tempVector128_5[0] = _mm_load_si128((const __m128i*)&_xmm);
    LOBYTE(tempPairBuffer2[0]) = 0;

    // Additional overloads for teleport command with rotation and facing options
    // Define checkForBlocks parameter
    typeIdBool = Bedrock::type_id<CommandRegistry, bool>(&tempStringPtr);
    CommandParameterData::CommandParameterData(
        (unsigned int)paramBuffer1, typeIdBool, (unsigned int)CommandRegistry::parse<bool>,
        (unsigned int)"checkForBlocks", 0, 0LL, 0LL, 751, 1, -1);

    // Define x rotation parameter
    int typeIdXRot = Bedrock::type_id<CommandRegistry, RelativeFloat>(tempByteBuffer);
    CommandParameterData::CommandParameterData(
        (unsigned int)paramBuffer3, typeIdXRot, (unsigned int)CommandRegistry::parse<RelativeFloat>,
        (unsigned int)"xRot", 0, 0LL, 0LL, 736, 1, -1);

    // Define y rotation parameter
    int typeIdYRot = Bedrock::type_id<CommandRegistry, RelativeFloat>(&tempStringPtr);
    CommandParameterData::CommandParameterData(
        (unsigned int)paramBuffer4, typeIdYRot, (unsigned int)CommandRegistry::parse<RelativeFloat>,
        (unsigned int)"yRot", 0, 0LL, 0LL, 728, 1, 749);

    // Define destination parameter
    int typeIdDest = Bedrock::type_id<CommandRegistry, CommandPositionFloat>(tempByteBuffer);
    CommandParameterData::CommandParameterData(
        (unsigned int)paramBuffer2, typeIdDest, (unsigned int)CommandRegistry::parse<CommandPositionFloat>,
        (unsigned int)"destination", 0, 0LL, 0LL, 496, 0, 748);

    // Register overload with rotation parameters
    CommandVersion::CommandVersion((CommandVersion*)tempCommandVersion, 1, 0x7FFFFFFF);
    CommandRegistry::_registerOverload<TeleportCommand, CommandParameterData, CommandParameterData, CommandParameterData, CommandParameterData>(
        registry, (__int64)paramBuffer4, (__int64)paramBuffer3, (__int64)paramBuffer1);

    // Clean up additional temporary buffers
    if (tempVector128_5[0].m128i_i64[1] >= 0x10uLL)
    {
        __int64 bufferPtr = tempPairBuffer2[0];
        if ((unsigned __int64)(tempVector128_5[0].m128i_i64[1] + 1) >= 0x1000)
        {
            bufferPtr = *(_QWORD*)(tempPairBuffer2[0] - 8LL);
            if ((unsigned __int64)(tempPairBuffer2[0] - bufferPtr - 8) > 0x1F)
                _invalid_parameter_noinfo_noreturn();
        }
        std::_Return_temporary_buffer<unsigned int>(bufferPtr);
    }
    tempVector128_5[0] = _mm_load_si128((const __m128i*)&_xmm);
    LOBYTE(tempPairBuffer2[0]) = 0;

    // Repeat cleanup for other temporary buffers
    if (tempVector128_4[0].m128i_i64[1] >= 0x10uLL)
    {
        __int64 bufferPtr = tempPairBuffer[0];
        if ((unsigned __int64)(tempVector128_4[0].m128i_i64[1] + 1) >= 0x1000)
        {
            bufferPtr = *(_QWORD*)(tempPairBuffer[0] - 8LL);
            if ((unsigned __int64)(tempPairBuffer[0] - bufferPtr - 8) > 0x1F)
                _invalid_parameter_noinfo_noreturn();
        }
        std::_Return_temporary_buffer<unsigned int>(bufferPtr);
    }
    tempVector128_4[0] = _mm_load_si128((const __m128i*)&_xmm);
    LOBYTE(tempPairBuffer[0]) = 0;

    // Define parameters for facing position overload
    typeIdBool = Bedrock::type_id<CommandRegistry, bool>(&tempStringPtr);
    CommandParameterData::CommandParameterData(
        (unsigned int)paramBuffer1, typeIdBool, (unsigned int)CommandRegistry::parse<bool>,
        (unsigned int)"checkForBlocks", 0, 0LL, 0LL, 751, 1, -1);

    int typeIdLookAtPos = Bedrock::type_id<CommandRegistry, CommandPositionFloat>(tempByteBuffer);
    CommandParameterData::CommandParameterData(
        (unsigned int)paramBuffer2, typeIdLookAtPos, (unsigned int)CommandRegistry::parse<CommandPositionFloat>,
        (unsigned int)"lookAtPosition", 0, 0LL, 0LL, 712, 0, 750);

    int typeIdFacing = Bedrock::type_id<CommandRegistry, enum RotationCommandUtils::FacingResult>(&tempStringPtr);
    CommandParameterData::CommandParameterData(
        (unsigned int)paramBuffer4, typeIdFacing, (unsigned int)JsonDefinitionSerializer<BlockClimberDefinition>::hasGetStrictParsingVersion,
        (unsigned int)"facing", 0, 0LL, 0LL, 744, 0, -1);

    typeIdDest = Bedrock::type_id<CommandRegistry, CommandPositionFloat>(tempByteBuffer);
    CommandParameterData::CommandParameterData(
        (unsigned int)paramBuffer3, typeIdDest, (unsigned int)CommandRegistry::parse<CommandPositionFloat>,
        (unsigned int)"destination", 0, 0LL, 0LL, 496, 0, 748);

    // Register overload with facing position
    CommandVersion::CommandVersion((CommandVersion*)tempCommandVersion, 1, 0x7FFFFFFF);
    CommandRegistry::_registerOverload<TeleportCommand, CommandParameterData, CommandParameterData, CommandParameterData, CommandParameterData>(
        registry, (__int64)paramBuffer4, (__int64)paramBuffer2, (__int64)paramBuffer1);

    // Clean up temporary buffers
    if (tempVector128_3[0].m128i_i64[1] >= 0x10uLL)
    {
        __int64 bufferPtr = tempPairBuffer[0];
        if ((unsigned __int64)(tempVector128_3[0].m128i_i64[1] + 1) >= 0x1000)
        {
            bufferPtr = *(_QWORD*)(tempPairBuffer[0] - 8LL);
            if ((unsigned __int64)(tempPairBuffer[0] - bufferPtr - 8) > 0x1F)
                _invalid_parameter_noinfo_noreturn();
        }
        std::_Return_temporary_buffer<unsigned int>(bufferPtr);
    }
    tempVector128_3[0] = _mm_load_si128((const __m128i*)&_xmm);
    LOBYTE(tempPairBuffer[0]) = 0;

    // Define parameters for facing entity overload
    typeIdBool = Bedrock::type_id<CommandRegistry, bool>(&tempStringPtr);
    CommandParameterData::CommandParameterData(
        (unsigned int)paramBuffer1, typeIdBool, (unsigned int)CommandRegistry::parse<bool>,
        (unsigned int)"checkForBlocks", 0, 0LL, 0LL, 751, 1, -1);

    int typeIdLookAtEntity = Bedrock::type_id<CommandRegistry, CommandSelector<Actor>>(tempByteBuffer);
    CommandParameterData::CommandParameterData(
        (unsigned int)paramBuffer2, typeIdLookAtEntity, (unsigned int)CommandRegistry::parse<CommandSelector<Actor>>,
        (unsigned int)"lookAtEntity", 0, 0LL, 0LL, 512, 0, -1);

    typeIdFacing = Bedrock::type_id<CommandRegistry, enum RotationCommandUtils::FacingResult>(&tempStringPtr);
    CommandParameterData::CommandParameterData(
        (unsigned int)paramBuffer4, typeIdFacing, (unsigned int)JsonDefinitionSerializer<BlockClimberDefinition>::hasGetStrictParsingVersion,
        (unsigned int)"facing", 0, 0LL, 0LL, 744, 0, -1);

    typeIdDest = Bedrock::type_id<CommandRegistry, CommandPositionFloat>(tempByteBuffer);
    CommandParameterData::CommandParameterData(
        (unsigned int)paramBuffer3, typeIdDest, (unsigned int)CommandRegistry::parse<CommandPositionFloat>,
        (unsigned int)"destination", 0, 0LL, 0LL, 496, 0, 748);

    // Register overload with facing entity
    CommandVersion::CommandVersion((CommandVersion*)tempCommandVersion, 1, 0x7FFFFFFF);
    CommandRegistry::_registerOverload<TeleportCommand, CommandParameterData, CommandParameterData, CommandParameterData, CommandParameterData>(
        registry, (__int64)paramBuffer4, (__int64)paramBuffer2, (__int64)paramBuffer1);

    // Clean up temporary buffers
    if (tempVector128_3[0].m128i_i64[1] >= 0x10uLL)
    {
        __int64 bufferPtr = tempPairBuffer[0];
        if ((unsigned __int64)(tempVector128_3[0].m128i_i64[1] + 1) >= 0x1000)
        {
            bufferPtr = *(_QWORD*)(tempPairBuffer[0] - 8LL);
            if ((unsigned __int64)(tempPairBuffer[0] - bufferPtr - 8) > 0x1F)
                _invalid_parameter_noinfo_noreturn();
        }
        std::_Return_temporary_buffer<unsigned int>(bufferPtr);
    }
    tempVector128_3[0] = _mm_load_si128((const __m128i*)&_xmm);
    LOBYTE(tempPairBuffer[0]) = 0;

    // Define parameters for victim with rotation overload
    typeIdBool = Bedrock::type_id<CommandRegistry, bool>(&tempStringPtr);
    CommandParameterData::CommandParameterData(
        (unsigned int)paramBuffer5, typeIdBool, (unsigned int)CommandRegistry::parse<bool>,
        (unsigned int)"checkForBlocks", 0, 0LL, 0LL, 751, 1, -1);

    typeIdXRot = Bedrock::type_id<CommandRegistry, RelativeFloat>(tempByteBuffer);
    CommandParameterData::CommandParameterData(
        (unsigned int)paramBuffer1, typeIdXRot, (unsigned int)CommandRegistry::parse<RelativeFloat>,
        (unsigned int)"xRot", 0, 0LL, 0LL, 736, 1, -1);

    typeIdYRot = Bedrock::type_id<CommandRegistry, RelativeFloat>(&tempStringPtr);
    CommandParameterData::CommandParameterData(
        (unsigned int)paramBuffer2, typeIdYRot, (unsigned int)CommandRegistry::parse<RelativeFloat>,
        (unsigned int)"yRot", 0, 0LL, 0LL, 728, 1, 749);

    typeIdDest = Bedrock::type_id<CommandRegistry, CommandPositionFloat>(tempByteBuffer);
    CommandParameterData::CommandParameterData(
        (unsigned int)paramBuffer4, typeIdDest, (unsigned int)CommandRegistry::parse<CommandPositionFloat>,
        (unsigned int)"destination", 0, 0LL, 0LL, 496, 0, 748);

    int typeIdVictim = Bedrock::type_id<CommandRegistry, CommandSelector<Actor>>(&tempStringPtr);
    CommandParameterData::CommandParameterData(
        (unsigned int)paramBuffer3, typeIdVictim, (unsigned int)CommandRegistry::parse<CommandSelector<Actor>>,
        (unsigned int)"victim", 0, 0LL, 0LL, 96, 0, -1);

    // Register overload with victim and rotation
    CommandVersion::CommandVersion((CommandVersion*)tempCommandVersion, 1, 0x7FFFFFFF);
    CommandRegistry::_registerOverload<TeleportCommand, CommandParameterData, CommandParameterData, CommandParameterData, CommandParameterData, CommandParameterData>(
        registry, (__int64)paramBuffer4, (__int64)paramBuffer2, (__int64)paramBuffer1, (__int64)paramBuffer5);

    // Clean up temporary buffers
    if (tempVector128_3[0].m128i_i64[1] >= 0x10uLL)
    {
        __int64 bufferPtr = tempPairBuffer[0];
        if ((unsigned __int64)(tempVector128_3[0].m128i_i64[1] + 1) >= 0x1000)
        {
            bufferPtr = *(_QWORD*)(tempPairBuffer[0] - 8LL);
            if ((unsigned __int64)(tempPairBuffer[0] - bufferPtr - 8) > 0x1F)
                _invalid_parameter_noinfo_noreturn();
        }
        std::_Return_temporary_buffer<unsigned int>(bufferPtr);
    }
    tempVector128_3[0] = _mm_load_si128((const __m128i*)&_xmm);
    LOBYTE(tempPairBuffer[0]) = 0;

    // Define parameters for victim with destination overload
    typeIdBool = Bedrock::type_id<CommandRegistry, bool>(&tempStringPtr);
    CommandParameterData::CommandParameterData(
        (unsigned int)paramBuffer5, typeIdBool, (unsigned int)CommandRegistry::parse<bool>,
        (unsigned int)"checkForBlocks", 0, 0LL, 0LL, 751, 1, -1);

    typeIdDest = Bedrock::type_id<CommandRegistry, CommandPositionFloat>(tempByteBuffer);
    CommandParameterData::CommandParameterData(
        (unsigned int)paramBuffer3, typeIdDest, (unsigned int)CommandRegistry::parse<CommandPositionFloat>,
        (unsigned int)"destination", 0, 0LL, 0LL, 496, 0, 748);

    typeIdVictim = Bedrock::type_id<CommandRegistry, CommandSelector<Actor>>(&tempStringPtr);
    CommandParameterData::CommandParameterData(
        (unsigned int)paramBuffer1, typeIdVictim, (unsigned int)CommandRegistry::parse<CommandSelector<Actor>>,
        (unsigned int)"victim", 0, 0LL, 0LL, 96, 0, -1);

    // Register overload with victim and destination
    CommandVersion::CommandVersion((CommandVersion*)tempCommandVersion, 1, 0x7FFFFFFF);
    CommandRegistry::_registerOverload<TeleportCommand, CommandParameterData, CommandParameterData, CommandParameterData>(
        registry, (__int64)paramBuffer3, (__int64)paramBuffer5);

    // Clean up temporary buffers
    if (tempVector128_2[0].m128i_i64[1] >= 0x10uLL)
    {
        __int64 bufferPtr = tempPairBuffer[0];
        if ((unsigned __int64)(tempVector128_2[0].m128i_i64[1] + 1) >= 0x1000)
        {
            bufferPtr = *(_QWORD*)(tempPairBuffer[0] - 8LL);
            if ((unsigned __int64)(tempPairBuffer[0] - bufferPtr - 8) > 0x1F)
                _invalid_parameter_noinfo_noreturn();
        }
        std::_Return_temporary_buffer<unsigned int>(bufferPtr);
    }
    tempVector128_2[0] = _mm_load_si128((const __m128i*)&_xmm);
    LOBYTE(tempPairBuffer[0]) = 0;

    // Define parameters for victim with facing position overload
    typeIdBool = Bedrock::type_id<CommandRegistry, bool>(&tempStringPtr);
    CommandParameterData::CommandParameterData(
        (unsigned int)paramBuffer2, typeIdBool, (unsigned int)CommandRegistry::parse<bool>,
        (unsigned int)"checkForBlocks", 0, 0LL, 0LL, 751, 1, -1);

    typeIdLookAtPos = Bedrock::type_id<CommandRegistry, CommandPositionFloat>(tempByteBuffer);
    CommandParameterData::CommandParameterData(
        (unsigned int)paramBuffer4, typeIdLookAtPos, (unsigned int)CommandRegistry::parse<CommandPositionFloat>,
        (unsigned int)"lookAtPosition", 0, 0LL, 0LL, 712, 0, 750);

    typeIdFacing = Bedrock::type_id<CommandRegistry, enum RotationCommandUtils::FacingResult>(&tempStringPtr);
    CommandParameterData::CommandParameterData(
        (unsigned int)paramBuffer3, typeIdFacing, (unsigned int)JsonDefinitionSerializer<BlockClimberDefinition>::hasGetStrictParsingVersion,
        (unsigned int)"facing", 0, 0LL, 0LL, 744, 0, -1);

    typeIdDest = Bedrock::type_id<CommandRegistry, CommandPositionFloat>(tempByteBuffer);
    CommandParameterData::CommandParameterData(
        (unsigned int)paramBuffer1, typeIdDest, (unsigned int)CommandRegistry::parse<CommandPositionFloat>,
        (unsigned int)"destination", 0, 0LL, 0LL, 496, 0, 748);

    typeIdVictim = Bedrock::type_id<CommandRegistry, CommandSelector<Actor>>(&tempStringPtr);
    CommandParameterData::CommandParameterData(
        (unsigned int)paramBuffer5, typeIdVictim, (unsigned int)CommandRegistry::parse<CommandSelector<Actor>>,
        (unsigned int)"victim", 0, 0LL, 0LL, 96, 0, -1);

    // Register overload with victim and facing position
    CommandVersion::CommandVersion((CommandVersion*)tempCommandVersion, 1, 0x7FFFFFFF);
    CommandRegistry::_registerOverload<TeleportCommand, CommandParameterData, CommandParameterData, CommandParameterData, CommandParameterData, CommandParameterData>(
        registry, (__int64)paramBuffer1, (__int64)paramBuffer3, (__int64)paramBuffer4, (__int64)paramBuffer2);

    // Clean up pair buffers
    std::pair<std::string, enum LootCommand::Target>::~pair<std::string, enum LootCommand::Target>(tempPairBuffer2);
    std::pair<std::string, enum LootCommand::Target>::~pair<std::string, enum LootCommand::Target>(tempPairBuffer);
    std::pair<std::string, enum LootCommand::Target>::~pair<std::string, enum LootCommand::Target>(paramBuffer3);
    std::pair<std::string, enum LootCommand::Target>::~pair<std::string, enum LootCommand::Target>(paramBuffer4);
    std::pair<std::string, enum LootCommand::Target>::~pair<std::string, enum LootCommand::Target>(paramBuffer5);

    // Define parameters for victim with facing entity overload
    typeIdBool = Bedrock::type_id<CommandRegistry, bool>(&tempStringPtr);
    CommandParameterData::CommandParameterData(
        (unsigned int)paramBuffer2, typeIdBool, (unsigned int)CommandRegistry::parse<bool>,
        (unsigned int)"checkForBlocks", 0, 0LL, 0LL, 751, 1, -1);

    typeIdLookAtEntity = Bedrock::type_id<CommandRegistry, CommandSelector<Actor>>(tempByteBuffer);
    CommandParameterData::CommandParameterData(
        (unsigned int)paramBuffer4, typeIdLookAtEntity, (unsigned int)CommandRegistry::parse<CommandSelector<Actor>>,
        (unsigned int)"lookAtEntity", 0, 0LL, 0LL, 512, 0, -1);

    typeIdFacing = Bedrock::type_id<CommandRegistry, enum RotationCommandUtils::FacingResult>(&tempStringPtr);
    CommandParameterData::CommandParameterData(
        (unsigned int)paramBuffer3, typeIdFacing, (unsigned int)JsonDefinitionSerializer<BlockClimberDefinition>::hasGetStrictParsingVersion,
        (unsigned int)"facing", 0, 0LL, 0LL, 744, 0, -1);

    typeIdDest = Bedrock::type_id<CommandRegistry, CommandPositionFloat>(tempByteBuffer);
    CommandParameterData::CommandParameterData(
        (unsigned int)paramBuffer1, typeIdDest, (unsigned int)CommandRegistry::parse<CommandPositionFloat>,
        (unsigned int)"destination", 0, 0LL, 0LL, 496, 0, 748);

    typeIdVictim = Bedrock::type_id<CommandRegistry, CommandSelector<Actor>>(&tempStringPtr);
    CommandParameterData::CommandParameterData(
        (unsigned int)paramBuffer5, typeIdVictim, (unsigned int)CommandRegistry::parse<CommandSelector<Actor>>,
        (unsigned int)"victim", 0, 0LL, 0LL, 96, 0, -1);

    // Register overload with victim and facing entity
    CommandVersion::CommandVersion((CommandVersion*)tempCommandVersion, 1, 0x7FFFFFFF);
    CommandRegistry::_registerOverload<TeleportCommand, CommandParameterData, CommandParameterData, CommandParameterData, CommandParameterData, CommandParameterData>(
        registry, (__int64)paramBuffer1, (__int64)paramBuffer3, (__int64)paramBuffer4, (__int64)paramBuffer2);

    // Clean up pair buffers
    std::pair<std::string, enum LootCommand::Target>::~pair<std::string, enum LootCommand::Target>(tempPairBuffer2);
    std::pair<std::string, enum LootCommand::Target>::~pair<std::string, enum LootCommand::Target>(tempPairBuffer);
    std::pair<std::string, enum LootCommand::Target>::~pair<std::string, enum LootCommand::Target>(paramBuffer3);
    std::pair<std::string, enum LootCommand::Target>::~pair<std::string, enum LootCommand::Target>(paramBuffer4);
    std::pair<std::string, enum LootCommand::Target>::~pair<std::string, enum LootCommand::Target>(paramBuffer5);

    // Define parameter for destination entity
    typeIdDest = Bedrock::type_id<CommandRegistry, CommandSelector<Actor>>(&tempStringPtr);
    CommandParameterData::CommandParameterData(
        (unsigned int)paramBuffer5, typeIdDest, (unsigned int)CommandRegistry::parse<CommandSelector<Actor>>,
        (unsigned int)"destination", 0, 0LL, 0LL, 296, 0, -1);

    // Register overload with destination entity
    CommandVersion::CommandVersion((CommandVersion*)tempCommandVersion, 1, 0x7FFFFFFF);
    CommandRegistry::_registerOverload<TeleportCommand, CommandParameterData>(registry);

    // Clean up pair buffer
    std::pair<std::string, enum LootCommand::Target>::~pair<std::string, enum LootCommand::Target>(tempPairBuffer2);

    // Define parameters for victim and destination entity with checkForBlocks
    typeIdBool = Bedrock::type_id<CommandRegistry, bool>(&tempStringPtr);
    CommandParameterData::CommandParameterData(
        (unsigned int)paramBuffer3, typeIdBool, (unsigned int)CommandRegistry::parse<bool>,
        (unsigned int)"checkForBlocks", 0, 0LL, 0LL, 751, 1, -1);

    typeIdDest = Bedrock::type_id<CommandRegistry, CommandSelector<Actor>>(tempByteBuffer);
    CommandParameterData::CommandParameterData(
        (unsigned int)paramBuffer1, typeIdDest, (unsigned int)CommandRegistry::parse<CommandSelector<Actor>>,
        (unsigned int)"destination", 0, 0LL, 0LL, 296, 0, -1);

    typeIdVictim = Bedrock::type_id<CommandRegistry, CommandSelector<Actor>>(&tempStringPtr);
    CommandParameterData::CommandParameterData(
        (unsigned int)paramBuffer5, typeIdVictim, (unsigned int)CommandRegistry::parse<CommandSelector<Actor>>,
        (unsigned int)"victim", 0, 0LL, 0LL, 96, 0, -1);

    // Register overload with victim, destination, and checkForBlocks
    CommandVersion::CommandVersion((CommandVersion*)tempCommandVersion, 1, 0x7FFFFFFF);
    CommandRegistry::_registerOverload<TeleportCommand, CommandParameterData, CommandParameterData, CommandParameterData>(
        registry, (__int64)paramBuffer1, (__int64)paramBuffer3);

    // Final cleanup of pair buffers
    std::pair<std::string, enum LootCommand::Target>::~pair<std::string, enum LootCommand::Target>(tempPairBuffer2);
    std::pair<std::string, enum LootCommand::Target>::~pair<std::string, enum LootCommand::Target>(tempPairBuffer);
}