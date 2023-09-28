__int64 __fastcall CommandRegistry::parseSelector(
        __int64 a1,
        CommandSelectorBase *a2,
        __int64 a3,
        __int64 a4,
        int a5,
        __int64 a6,
        __int64 a7)
{
  const CommandRegistry::Symbol *v7; // rsi
  __int64 v8; // rax
  __int64 v9; // rax
  __int64 v10; // rax
  __int64 v11; // rax
  char v12; // dl
  __int64 v13; // rax
  __int64 v14; // rax
  __int64 v15; // rax
  __int64 v16; // rax
  int v18; // [rsp+48h] [rbp-778h]
  int v19; // [rsp+110h] [rbp-6B0h]
  char v20; // [rsp+27Fh] [rbp-541h]
  __int64 v21; // [rsp+2A0h] [rbp-520h]
  __int64 v22; // [rsp+2A8h] [rbp-518h]
  __int64 v23; // [rsp+2B8h] [rbp-508h]
  int v24; // [rsp+2C0h] [rbp-500h]
  char v25; // [rsp+2C7h] [rbp-4F9h]
  __int64 v26; // [rsp+2C8h] [rbp-4F8h]
  int v27; // [rsp+2D4h] [rbp-4ECh]
  __int64 i; // [rsp+2D8h] [rbp-4E8h]
  char v29; // [rsp+2E5h] [rbp-4DBh]
  char v30; // [rsp+2E6h] [rbp-4DAh]
  char v31; // [rsp+2E7h] [rbp-4D9h]
  __int64 v32; // [rsp+308h] [rbp-4B8h]
  char v38; // [rsp+34Fh] [rbp-471h]
  char v39[32]; // [rsp+358h] [rbp-468h] BYREF
  double v40; // [rsp+378h] [rbp-448h]
  char v41[32]; // [rsp+380h] [rbp-440h] BYREF
  double v42; // [rsp+3A0h] [rbp-420h]
  char v43[32]; // [rsp+3A8h] [rbp-418h] BYREF
  char v44[32]; // [rsp+3C8h] [rbp-3F8h] BYREF
  float v45; // [rsp+3E8h] [rbp-3D8h] BYREF
  float v46; // [rsp+3ECh] [rbp-3D4h] BYREF
  char v47[16]; // [rsp+3F0h] [rbp-3D0h] BYREF
  char v48[16]; // [rsp+400h] [rbp-3C0h] BYREF
  char v49[16]; // [rsp+410h] [rbp-3B0h] BYREF
  __int64 v50[2]; // [rsp+420h] [rbp-3A0h] BYREF
  int v51; // [rsp+434h] [rbp-38Ch] BYREF
  char v52[36]; // [rsp+438h] [rbp-388h] BYREF
  unsigned int v53; // [rsp+45Ch] [rbp-364h] BYREF
  char v54[8]; // [rsp+460h] [rbp-360h] BYREF
  char v55[8]; // [rsp+468h] [rbp-358h] BYREF
  char v56[8]; // [rsp+470h] [rbp-350h] BYREF
  char v57[4]; // [rsp+478h] [rbp-348h] BYREF
  char v58[4]; // [rsp+47Ch] [rbp-344h] BYREF
  char v59[8]; // [rsp+480h] [rbp-340h] BYREF
  char v60[4]; // [rsp+488h] [rbp-338h] BYREF
  char v61[4]; // [rsp+48Ch] [rbp-334h] BYREF
  char v62[4]; // [rsp+490h] [rbp-330h] BYREF
  char v63; // [rsp+494h] [rbp-32Ch] BYREF
  char v64; // [rsp+495h] [rbp-32Bh] BYREF
  char v65[2]; // [rsp+496h] [rbp-32Ah] BYREF
  int v66; // [rsp+498h] [rbp-328h] BYREF
  int v67; // [rsp+49Ch] [rbp-324h] BYREF
  __int64 v68; // [rsp+4A0h] [rbp-320h] BYREF
  int v69; // [rsp+4A8h] [rbp-318h] BYREF
  int v70; // [rsp+4ACh] [rbp-314h] BYREF
  double v71; // [rsp+4B0h] [rbp-310h] BYREF
  int v72; // [rsp+4B8h] [rbp-308h] BYREF
  int v73; // [rsp+4BCh] [rbp-304h] BYREF
  double v74; // [rsp+4C0h] [rbp-300h] BYREF
  char v75[8]; // [rsp+4C8h] [rbp-2F8h] BYREF
  char v76[8]; // [rsp+4D0h] [rbp-2F0h] BYREF
  char v77[48]; // [rsp+4D8h] [rbp-2E8h] BYREF
  char v78[48]; // [rsp+508h] [rbp-2B8h] BYREF
  char v79[8]; // [rsp+538h] [rbp-288h] BYREF
  char v80[8]; // [rsp+540h] [rbp-280h] BYREF
  __int64 v81; // [rsp+548h] [rbp-278h] BYREF
  int v82; // [rsp+550h] [rbp-270h]
  __int64 v83; // [rsp+558h] [rbp-268h]
  __int64 v84; // [rsp+560h] [rbp-260h]
  char v85; // [rsp+568h] [rbp-258h]
  __int64 v86; // [rsp+570h] [rbp-250h] BYREF
  int v87; // [rsp+578h] [rbp-248h]
  char v88[32]; // [rsp+580h] [rbp-240h] BYREF
  __int64 v89; // [rsp+5A0h] [rbp-220h]
  char v90[32]; // [rsp+5A8h] [rbp-218h] BYREF
  char v91[32]; // [rsp+5C8h] [rbp-1F8h] BYREF
  char v92[32]; // [rsp+5E8h] [rbp-1D8h] BYREF
  char v93[32]; // [rsp+608h] [rbp-1B8h] BYREF
  char v94[32]; // [rsp+628h] [rbp-198h] BYREF
  char v95[32]; // [rsp+648h] [rbp-178h] BYREF
  char v96[40]; // [rsp+668h] [rbp-158h] BYREF
  char v97[32]; // [rsp+690h] [rbp-130h] BYREF
  char v98[40]; // [rsp+6B0h] [rbp-110h] BYREF
  char v99[32]; // [rsp+6D8h] [rbp-E8h] BYREF
  char v100[40]; // [rsp+6F8h] [rbp-C8h] BYREF
  char v101[40]; // [rsp+720h] [rbp-A0h] BYREF
  char v102[32]; // [rsp+748h] [rbp-78h] BYREF
  char v103[4]; // [rsp+768h] [rbp-58h] BYREF
  int v104; // [rsp+76Ch] [rbp-54h] BYREF
  __int64 v105; // [rsp+770h] [rbp-50h] BYREF
  char v106[32]; // [rsp+778h] [rbp-48h] BYREF
  char v107[32]; // [rsp+798h] [rbp-28h] BYREF
  unsigned __int64 v108; // [rsp+7B8h] [rbp-8h]

  v108 = __readfsqword(0x28u);
  if ( !a2 )
  {
    v38 = 0;
    return v38 & 1;
  }
  CommandSelectorBase::setVersion(a2, a5);
  v32 = std::unique_ptr<CommandRegistry::ParseToken,std::default_delete<CommandRegistry::ParseToken>>::get(a3);
  CommandRegistry::Symbol::Symbol(v80, 1048607LL);
  v20 = 1;
  if ( (CommandRegistry::Symbol::operator==(v32 + 36, v80) & 1) == 0 )
  {
    CommandRegistry::Symbol::Symbol(v79, 14LL);
    v20 = CommandRegistry::Symbol::operator==(v32 + 36, v79);
  }
  if ( (v20 & 1) != 0 )
  {
    CommandRegistry::ParseToken::toString[abi:cxx11](v106, a3);
    CommandRegistry::_removeStringQuotes(v107, v106);
    CommandSelectorBase::setExplicitIdSelector(a2, v107);
    std::__cxx11::basic_string<char,std::char_traits<char>,std::allocator<char>>::~basic_string(v107);
    std::__cxx11::basic_string<char,std::char_traits<char>,std::allocator<char>>::~basic_string(v106);
    v38 = CommandSelectorBase::compile(a2, a4, a6) & 1;
    return v38 & 1;
  }
  switch ( *(_BYTE *)(*(_QWORD *)(v32 + 24) + 1LL) )
  {
    case 'a':
      CommandSelectorBase::setType(a2, 2LL);
      CommandSelectorBase::setIncludeDeadPlayers(a2, 1);
      break;
    case 'c':
      CommandSelectorBase::setType(a2, 4LL);
      CommandSelectorBase::setResultCount(a2, 1uLL);
      break;
    case 'e':
      CommandSelectorBase::setType(a2, 1LL);
      break;
    case 'p':
      CommandSelectorBase::setType(a2, 2LL);
      CommandSelectorBase::setResultCount(a2, 1uLL);
      break;
    case 'r':
      CommandSelectorBase::setType(a2, 3LL);
      CommandSelectorBase::setOrder(a2, 2LL);
      CommandSelectorBase::setResultCount(a2, 1uLL);
      break;
    case 'v':
      CommandSelectorBase::setType(a2, 5LL);
      break;
    default:
      break;
  }
  if ( (std::unique_ptr<CommandRegistry::ParseToken,std::default_delete<CommandRegistry::ParseToken>>::operator bool(v32 + 8) & 1) == 0 )
  {
    v38 = CommandSelectorBase::compile(a2, a4, a6) & 1;
    return v38 & 1;
  }
  std::set<CommandRegistry::Symbol,std::less<CommandRegistry::Symbol>,std::allocator<CommandRegistry::Symbol>>::set(v78);
  CommandRegistry::Symbol::Symbol((CommandRegistry::Symbol *)v103, (const CommandRegistry::Symbol *)(a1 + 832));
  CommandRegistry::Symbol::Symbol((CommandRegistry::Symbol *)&v104, (const CommandRegistry::Symbol *)(a1 + 828));
  CommandRegistry::Symbol::Symbol((CommandRegistry::Symbol *)&v105, (const CommandRegistry::Symbol *)(a1 + 824));
  CommandRegistry::Symbol::Symbol(
    (CommandRegistry::Symbol *)((char *)&v105 + 4),
    (const CommandRegistry::Symbol *)(a1 + 840));
  std::allocator<CommandRegistry::Symbol>::allocator(v75);
  std::set<CommandRegistry::Symbol,std::less<CommandRegistry::Symbol>,std::allocator<CommandRegistry::Symbol>>::set(
    v77,
    v103,
    4LL,
    v76,
    v75);
  std::allocator<CommandRegistry::Symbol>::~allocator(v75);
  v73 = -90;
  v72 = 90;
  std::pair<float,float>::pair<int,int,true>(&v74, &v73, &v72);
  v31 = 0;
  v70 = -180;
  v69 = 180;
  std::pair<float,float>::pair<int,int,true>(&v71, &v70, &v69);
  v30 = 0;
  v67 = 0;
  v66 = std::numeric_limits<int>::max();
  std::pair<int,int>::pair<int,int,true>(&v68, &v67, &v66);
  v29 = 0;
  CommandPosition::CommandPosition((CommandPosition *)v60);
  BlockPos::BlockPos((BlockPos *)v57);
  for ( i = std::unique_ptr<CommandRegistry::ParseToken,std::default_delete<CommandRegistry::ParseToken>>::get(v32 + 8);
        i;
        i = std::unique_ptr<CommandRegistry::ParseToken,std::default_delete<CommandRegistry::ParseToken>>::get(i + 8) )
  {
    v7 = (const CommandRegistry::Symbol *)(std::unique_ptr<CommandRegistry::ParseToken,std::default_delete<CommandRegistry::ParseToken>>::operator->(i)
                                         + 36);
    CommandRegistry::Symbol::Symbol((CommandRegistry::Symbol *)v56, v7);
    v8 = std::unique_ptr<CommandRegistry::ParseToken,std::default_delete<CommandRegistry::ParseToken>>::operator->(i);
    if ( (std::unique_ptr<CommandRegistry::ParseToken,std::default_delete<CommandRegistry::ParseToken>>::operator bool(v8) & 1) != 0 )
    {
      v9 = std::unique_ptr<CommandRegistry::ParseToken,std::default_delete<CommandRegistry::ParseToken>>::operator->(i);
      v10 = std::unique_ptr<CommandRegistry::ParseToken,std::default_delete<CommandRegistry::ParseToken>>::operator->(v9);
      CommandRegistry::Symbol::operator=(v56, v10 + 36);
    }
    if ( std::set<CommandRegistry::Symbol,std::less<CommandRegistry::Symbol>,std::allocator<CommandRegistry::Symbol>>::count(
           v78,
           v56) )
    {
      std::__cxx11::basic_string<char,std::char_traits<char>,std::allocator<char>>::operator=(
        a6,
        "commands.generic.duplicateSelectorArgument");
      v11 = std::unique_ptr<CommandRegistry::ParseToken,std::default_delete<CommandRegistry::ParseToken>>::operator->(i);
      CommandRegistry::ParseToken::toString[abi:cxx11](v102, v11);
      std::vector<std::__cxx11::basic_string<char,std::char_traits<char>,std::allocator<char>>,std::allocator<std::__cxx11::basic_string<char,std::char_traits<char>,std::allocator<char>>>>::emplace_back<std::__cxx11::basic_string<char,std::char_traits<char>,std::allocator<char>>>(
        a7,
        v102);
      std::__cxx11::basic_string<char,std::char_traits<char>,std::allocator<char>>::~basic_string(v102);
      v38 = 0;
      goto LABEL_150;
    }
    if ( !std::set<CommandRegistry::Symbol,std::less<CommandRegistry::Symbol>,std::allocator<CommandRegistry::Symbol>>::count(
            v77,
            v56) )
    {
      v84 = std::set<CommandRegistry::Symbol,std::less<CommandRegistry::Symbol>,std::allocator<CommandRegistry::Symbol>>::insert(
              v78,
              v56);
      v85 = v12;
    }
    v13 = std::unique_ptr<CommandRegistry::ParseToken,std::default_delete<CommandRegistry::ParseToken>>::operator->(i);
    v26 = std::unique_ptr<CommandRegistry::ParseToken,std::default_delete<CommandRegistry::ParseToken>>::get(v13 + 8);
    if ( (CommandRegistry::Symbol::operator==(v56, a1 + 828) & 1) != 0 )
    {
      CommandRegistry::getInvertableFilter[abi:cxx11](v101, a1, v26);
      CommandSelectorBase::addTypeFilter(a2, v101);
      InvertableFilter<std::__cxx11::basic_string<char,std::char_traits<char>,std::allocator<char>>>::~InvertableFilter(v101);
      continue;
    }
    if ( (CommandRegistry::Symbol::operator==(v56, a1 + 840) & 1) != 0 )
    {
      CommandRegistry::getInvertableFilter[abi:cxx11](v100, a1, v26);
      CommandRegistry::_removeStringQuotes(v99, v100);
      std::__cxx11::basic_string<char,std::char_traits<char>,std::allocator<char>>::operator=(v100, v99);
      std::__cxx11::basic_string<char,std::char_traits<char>,std::allocator<char>>::~basic_string(v99);
      CommandSelectorBase::addTagFilter(a2, v100);
      InvertableFilter<std::__cxx11::basic_string<char,std::char_traits<char>,std::allocator<char>>>::~InvertableFilter(v100);
      continue;
    }
    if ( (CommandRegistry::Symbol::operator==(v56, a1 + 824) & 1) != 0 )
    {
      CommandRegistry::getInvertableFilter[abi:cxx11](v98, a1, v26);
      CommandRegistry::_removeStringQuotes(v97, v98);
      std::__cxx11::basic_string<char,std::char_traits<char>,std::allocator<char>>::operator=(v98, v97);
      std::__cxx11::basic_string<char,std::char_traits<char>,std::allocator<char>>::~basic_string(v97);
      CommandSelectorBase::addNameFilter(a2, v98);
      InvertableFilter<std::__cxx11::basic_string<char,std::char_traits<char>,std::allocator<char>>>::~InvertableFilter(v98);
      continue;
    }
    if ( (CommandRegistry::Symbol::operator==(v56, a1 + 832) & 1) != 0 )
    {
      CommandRegistry::getInvertableFilter[abi:cxx11](v96, a1, v26);
      CommandRegistry::_removeStringQuotes(v95, v96);
      std::__cxx11::basic_string<char,std::char_traits<char>,std::allocator<char>>::operator=(v96, v95);
      std::__cxx11::basic_string<char,std::char_traits<char>,std::allocator<char>>::~basic_string(v95);
      CommandSelectorBase::addFamilyFilter(a2, v96);
      InvertableFilter<std::__cxx11::basic_string<char,std::char_traits<char>,std::allocator<char>>>::~InvertableFilter(v96);
      continue;
    }
    if ( (CommandRegistry::Symbol::operator==(v56, a1 + 820) & 1) != 0 )
    {
      v25 = 0;
      v24 = -1;
      v14 = std::unique_ptr<CommandRegistry::ParseToken,std::default_delete<CommandRegistry::ParseToken>>::operator->(i);
      v23 = std::unique_ptr<CommandRegistry::ParseToken,std::default_delete<CommandRegistry::ParseToken>>::get(v14 + 8);
      CommandRegistry::Symbol::Symbol(v55, 12LL);
      if ( (CommandRegistry::Symbol::operator==(v23 + 36, v55) & 1) != 0 )
      {
        v25 = 1;
        v23 = std::unique_ptr<CommandRegistry::ParseToken,std::default_delete<CommandRegistry::ParseToken>>::get(v23 + 8);
      }
      CommandRegistry::Symbol::Symbol(v54, 1LL);
      if ( (CommandRegistry::Symbol::operator==(v23 + 36, v54) & 1) != 0 )
      {
        if ( (CommandRegistry::readInt(&v53, v23, a6, a7) & 1) == 0 )
        {
          v38 = 0;
          goto LABEL_150;
        }
        if ( v53 >= 3 )
        {
          std::__cxx11::basic_string<char,std::char_traits<char>,std::allocator<char>>::operator=(
            a6,
            "commands.gamemode.fail.invalid");
          Util::toString<int,(void *)0,(void *)0>(v94, v53);
          std::vector<std::__cxx11::basic_string<char,std::char_traits<char>,std::allocator<char>>,std::allocator<std::__cxx11::basic_string<char,std::char_traits<char>,std::allocator<char>>>>::emplace_back<std::__cxx11::basic_string<char,std::char_traits<char>,std::allocator<char>>>(
            a7,
            v94);
          std::__cxx11::basic_string<char,std::char_traits<char>,std::allocator<char>>::~basic_string(v94);
          v38 = 0;
          goto LABEL_150;
        }
        v24 = v53;
      }
      else if ( (CommandRegistry::Symbol::isEnum((CommandRegistry::Symbol *)(v23 + 36)) & 1) != 0 )
      {
        v23 = std::unique_ptr<CommandRegistry::ParseToken,std::default_delete<CommandRegistry::ParseToken>>::get(v23);
        switch ( **(_BYTE **)(v23 + 24) )
        {
          case 's':
            v24 = 0;
            break;
          case 'c':
            v24 = 1;
            break;
          case 'a':
            v24 = 2;
            break;
        }
      }
      if ( v24 == -1 )
      {
        std::__cxx11::basic_string<char,std::char_traits<char>,std::allocator<char>>::operator=(
          a6,
          "commands.gamemode.fail.invalid");
        CommandRegistry::ParseToken::toString[abi:cxx11](v93, v23);
        std::vector<std::__cxx11::basic_string<char,std::char_traits<char>,std::allocator<char>>,std::allocator<std::__cxx11::basic_string<char,std::char_traits<char>,std::allocator<char>>>>::emplace_back<std::__cxx11::basic_string<char,std::char_traits<char>,std::allocator<char>>>(
          a7,
          v93);
        std::__cxx11::basic_string<char,std::char_traits<char>,std::allocator<char>>::~basic_string(v93);
        v38 = 0;
        goto LABEL_150;
      }
      LODWORD(v83) = v24;
      BYTE4(v83) = v25 & 1;
      sub_F2221B0(v52, v83);
      CommandSelectorBase::addFilter(a2, v52);
      std::function<bool ()(CommandOrigin const&,Actor const&)>::~function(v52);
    }
    else if ( (CommandRegistry::Symbol::operator==(v56, a1 + 816) & 1) != 0 )
    {
      v51 = 0;
      CommandRegistry::ParseToken::toString[abi:cxx11](v92, v26);
      gsl::basic_string_span<char const,-1l>::basic_string_span<std::__cxx11::basic_string<char,std::char_traits<char>,std::allocator<char>>,void>(
        v50,
        v92);
      v19 = Util::toInt<int,(void *)0>(v50[0], v50[1], &v51);
      std::__cxx11::basic_string<char,std::char_traits<char>,std::allocator<char>>::~basic_string(v92);
      if ( v19 )
      {
        std::__cxx11::basic_string<char,std::char_traits<char>,std::allocator<char>>::operator=(
          a6,
          "Invalid entity count requested");
        v38 = 0;
        goto LABEL_150;
      }
      if ( !v51 )
      {
        std::__cxx11::basic_string<char,std::char_traits<char>,std::allocator<char>>::operator=(
          a6,
          "Entity count cannot be 0");
        v38 = 0;
        goto LABEL_150;
      }
      if ( v51 < 0 )
      {
        if ( (unsigned int)CommandSelectorBase::getOrder(a2) == 2 )
        {
          std::__cxx11::basic_string<char,std::char_traits<char>,std::allocator<char>>::operator=(
            a6,
            "Cannot inverse sort a random selection");
          v38 = 0;
          goto LABEL_150;
        }
        v51 = -v51;
        CommandSelectorBase::setOrder(a2, 1LL);
      }
      CommandSelectorBase::setResultCount(a2, v51);
    }
    else
    {
      if ( (CommandRegistry::Symbol::operator==(v56, a1 + 760) & 1) != 0 )
      {
        if ( (CommandRegistry::readRelativeCoordinate(&v63, v60, v26, 0LL, a6, a7) & 1) == 0 )
        {
          v38 = 0;
          goto LABEL_150;
        }
LABEL_72:
        CommandSelectorBase::setPosition(a2, (const CommandPosition *)v60);
        continue;
      }
      if ( (CommandRegistry::Symbol::operator==(v56, a1 + 764) & 1) != 0 )
      {
        if ( (CommandRegistry::readRelativeCoordinate(&v64, v61, v26, 0LL, a6, a7) & 1) == 0 )
        {
          v38 = 0;
          goto LABEL_150;
        }
        goto LABEL_72;
      }
      if ( (CommandRegistry::Symbol::operator==(v56, a1 + 768) & 1) != 0 )
      {
        if ( (CommandRegistry::readRelativeCoordinate(v65, v62, v26, 0LL, a6, a7) & 1) == 0 )
        {
          v38 = 0;
          goto LABEL_150;
        }
        goto LABEL_72;
      }
      if ( (CommandRegistry::Symbol::operator==(v56, a1 + 772) & 1) != 0 )
      {
        if ( (CommandRegistry::readInt(v57, v26, a6, a7) & 1) == 0 )
        {
          v38 = 0;
          goto LABEL_150;
        }
        BlockPos::BlockPos((BlockPos *)v49, (const BlockPos *)v57);
        CommandSelectorBase::setBox(a2, v49);
      }
      else if ( (CommandRegistry::Symbol::operator==(v56, a1 + 776) & 1) != 0 )
      {
        if ( (CommandRegistry::readInt(v58, v26, a6, a7) & 1) == 0 )
        {
          v38 = 0;
          goto LABEL_150;
        }
        BlockPos::BlockPos((BlockPos *)v48, (const BlockPos *)v57);
        CommandSelectorBase::setBox(a2, v48);
      }
      else if ( (CommandRegistry::Symbol::operator==(v56, a1 + 780) & 1) != 0 )
      {
        if ( (CommandRegistry::readInt(v59, v26, a6, a7) & 1) == 0 )
        {
          v38 = 0;
          goto LABEL_150;
        }
        BlockPos::BlockPos((BlockPos *)v47, (const BlockPos *)v57);
        CommandSelectorBase::setBox(a2, v47);
      }
      else if ( (CommandRegistry::Symbol::operator==(v56, a1 + 784) & 1) != 0 )
      {
        if ( (CommandRegistry::readFloat(&v46, v26, a6, a7) & 1) == 0 )
        {
          v38 = 0;
          goto LABEL_150;
        }
        if ( v46 < 0.0 )
        {
LABEL_89:
          std::__cxx11::basic_string<char,std::char_traits<char>,std::allocator<char>>::operator=(
            a6,
            "commands.generic.radiusNegative");
          v38 = 0;
          goto LABEL_150;
        }
        CommandSelectorBase::setRadiusMax(a2, v46);
      }
      else if ( (CommandRegistry::Symbol::operator==(v56, a1 + 788) & 1) != 0 )
      {
        if ( (CommandRegistry::readFloat(&v45, v26, a6, a7) & 1) == 0 )
        {
          v38 = 0;
          goto LABEL_150;
        }
        if ( v45 < 0.0 )
          goto LABEL_89;
        CommandSelectorBase::setRadiusMin(a2, v45);
      }
      else if ( (CommandRegistry::Symbol::operator==(v56, a1 + 792) & 1) != 0 )
      {
        if ( (CommandRegistry::readFloat((char *)&v74 + 4, v26, a6, a7) & 1) == 0 )
        {
          v38 = 0;
          goto LABEL_150;
        }
        if ( *((float *)&v74 + 1) > 90.0 || *((float *)&v74 + 1) < -90.0 )
          goto LABEL_122;
        v31 = 1;
      }
      else if ( (CommandRegistry::Symbol::operator==(v56, a1 + 796) & 1) != 0 )
      {
        if ( (CommandRegistry::readFloat(&v74, v26, a6, a7) & 1) == 0 )
        {
          v38 = 0;
          goto LABEL_150;
        }
        if ( *(float *)&v74 > 90.0 || *(float *)&v74 < -90.0 )
          goto LABEL_122;
        v31 = 1;
      }
      else if ( (CommandRegistry::Symbol::operator==(v56, a1 + 800) & 1) != 0 )
      {
        if ( (CommandRegistry::readFloat((char *)&v71 + 4, v26, a6, a7) & 1) == 0 )
        {
          v38 = 0;
          goto LABEL_150;
        }
        if ( *((float *)&v71 + 1) > 180.0 || *((float *)&v71 + 1) < -180.0 )
          goto LABEL_122;
        v30 = 1;
      }
      else if ( (CommandRegistry::Symbol::operator==(v56, a1 + 804) & 1) != 0 )
      {
        if ( (CommandRegistry::readFloat(&v71, v26, a6, a7) & 1) == 0 )
        {
          v38 = 0;
          goto LABEL_150;
        }
        if ( *(float *)&v71 > 180.0 || *(float *)&v71 < -180.0 )
        {
LABEL_122:
          std::__cxx11::basic_string<char,std::char_traits<char>,std::allocator<char>>::operator=(
            a6,
            "commands.generic.rotationError");
          v38 = 0;
          goto LABEL_150;
        }
        v30 = 1;
      }
      else if ( (CommandRegistry::Symbol::operator==(v56, a1 + 808) & 1) != 0 )
      {
        if ( (CommandRegistry::readInt((char *)&v68 + 4, v26, a6, a7) & 1) == 0 )
        {
          v38 = 0;
          goto LABEL_150;
        }
        v29 = 1;
      }
      else if ( (CommandRegistry::Symbol::operator==(v56, a1 + 812) & 1) != 0 )
      {
        if ( (CommandRegistry::readInt(&v68, v26, a6, a7) & 1) == 0 )
        {
          v38 = 0;
          goto LABEL_150;
        }
        v29 = 1;
      }
      else if ( (CommandRegistry::Symbol::operator==(v56, a1 + 836) & 1) != 0 )
      {
        v15 = std::unique_ptr<CommandRegistry::ParseToken,std::default_delete<CommandRegistry::ParseToken>>::operator->(i);
        v16 = std::unique_ptr<CommandRegistry::ParseToken,std::default_delete<CommandRegistry::ParseToken>>::operator->(v15 + 8);
        v22 = std::unique_ptr<CommandRegistry::ParseToken,std::default_delete<CommandRegistry::ParseToken>>::get(v16);
        while ( v22 )
        {
          v21 = std::unique_ptr<CommandRegistry::ParseToken,std::default_delete<CommandRegistry::ParseToken>>::get(v22);
          CommandRegistry::ParseToken::toString[abi:cxx11](v90, v21);
          CommandRegistry::_removeStringQuotes(v91, v90);
          std::__cxx11::basic_string<char,std::char_traits<char>,std::allocator<char>>::~basic_string(v90);
          CommandIntegerRange::CommandIntegerRange((CommandIntegerRange *)&v81);
          v18 = std::unique_ptr<CommandRegistry::ParseToken,std::default_delete<CommandRegistry::ParseToken>>::operator*(v21 + 8);
          if ( (CommandRegistry::parse<CommandIntegerRange>(a1, (unsigned int)&v81, v18, a4, a5, a6, a7) & 1) != 0 )
          {
            v87 = v82;
            v86 = v81;
            std::__cxx11::basic_string<char,std::char_traits<char>,std::allocator<char>>::basic_string(v88, v91);
            v89 = a1 + 32;
            sub_F222280(v44, &v86);
            CommandSelectorBase::addFilter(a2, v44);
            std::function<bool ()(CommandOrigin const&,Actor const&)>::~function(v44);
            sub_F222320(&v86);
            v22 = std::unique_ptr<CommandRegistry::ParseToken,std::default_delete<CommandRegistry::ParseToken>>::get(v22 + 8);
            v27 = 0;
          }
          else
          {
            v38 = 0;
            v27 = 1;
          }
          std::__cxx11::basic_string<char,std::char_traits<char>,std::allocator<char>>::~basic_string(v91);
          if ( v27 )
            goto LABEL_150;
        }
      }
    }
  }
  if ( (v31 & 1) != 0 )
  {
    v42 = v74;
    sub_F222340(v43, v74);
    CommandSelectorBase::addFilter(a2, v43);
    std::function<bool ()(CommandOrigin const&,Actor const&)>::~function(v43);
  }
  if ( (v30 & 1) != 0 )
  {
    v40 = v71;
    sub_F222410(v41, v71);
    CommandSelectorBase::addFilter(a2, v41);
    std::function<bool ()(CommandOrigin const&,Actor const&)>::~function(v41);
  }
  if ( (v29 & 1) != 0 )
  {
    if ( SHIDWORD(v68) < (int)v68 )
    {
      std::__cxx11::basic_string<char,std::char_traits<char>,std::allocator<char>>::operator=(
        a6,
        "commands.generic.levelError");
      v38 = 0;
      goto LABEL_150;
    }
    sub_F2224E0(v39, v68);
    CommandSelectorBase::addFilter(a2, v39);
    std::function<bool ()(CommandOrigin const&,Actor const&)>::~function(v39);
  }
  v38 = CommandSelectorBase::compile(a2, a4, a6) & 1;
LABEL_150:
  std::set<CommandRegistry::Symbol,std::less<CommandRegistry::Symbol>,std::allocator<CommandRegistry::Symbol>>::~set(v77);
  std::set<CommandRegistry::Symbol,std::less<CommandRegistry::Symbol>,std::allocator<CommandRegistry::Symbol>>::~set(v78);
  return v38 & 1;
}