Ge// Function: CommandRegistry::setupOverloadRules
// Purpose: This function sets up overload rules for a given command signature and overload. 
// It ensures the associated parse rules and symbols are correctly applied, updated, or removed as needed.
// The function processes rules, checks optional/overloaded rules, builds new rules dynamically, 
// and performs memory cleanup for intermediate data structures.

void __fastcall CommandRegistry::setupOverloadRules(
        CommandRegistry *this,
        struct CommandRegistry::Signature *a2,
        struct CommandRegistry::Overload *a3)
{
  int v5; // ebx
  int v6; // edi
  __int64 v7; // rsi
  char *v8; // r9
  char *v9; // rax
  char *v10; // r8
  __int64 v11; // r12
  __int64 v12; // r15
  char *v13; // rsi
  __int64 v14; // rax
  __int64 v15; // rax
  __int64 v16; // rax
  __int64 v17; // rax
  __int64 v18; // r15
  __int64 v19; // rsi
  __int64 *v20; // rdx
  int v21; // eax
  _BYTE *v22; // rcx
  unsigned __int64 v23; // rdx
  _BYTE *v24; // rcx
  unsigned __int64 v25; // rdx
  int v26; // [rsp+30h] [rbp-79h] BYREF
  int v27; // [rsp+34h] [rbp-75h] BYREF
  __int64 v28; // [rsp+38h] [rbp-71h] BYREF
  void ***v29; // [rsp+40h] [rbp-69h] BYREF
  void *v30; // [rsp+48h] [rbp-61h] BYREF
  __int128 v31; // [rsp+50h] [rbp-59h]
  void **v32; // [rsp+60h] [rbp-49h] BYREF
  void *v33; // [rsp+68h] [rbp-41h]
  void ***v34; // [rsp+98h] [rbp-11h]
  __int64 v35; // [rsp+A0h] [rbp-9h]
  void *v36; // [rsp+A8h] [rbp-1h] BYREF
  __int128 v37; // [rsp+B0h] [rbp+7h]

  v35 = -2LL;
  v5 = *((_DWORD *)a2 + 23);
  v26 = v5;
  v6 = *((_DWORD *)a2 + 24);
  v7 = *((int *)a2 + 26);
  if ( (_DWORD)v7 != -1 )
  {
    v8 = (char *)*((_QWORD *)this + 17);
    v9 = v8;
    v10 = (char *)*((_QWORD *)this + 16);
    if ( v8 != v10 )
    {
      do
        v9 -= 104;
      while ( *(_DWORD *)v9 != 1048631 && v9 != v10 );
    }
    v11 = *((int *)a2 + 26);
    if ( v7 != (v8 - v10) / 104 )
    {
      v12 = 104 * v7;
      do
      {
        if ( (*(_DWORD *)&v10[v12] & 0x4200000) != 0 )
        {
          CommandRegistry::ParseRule::operator=(&v10[104 * (int)v7]);
          LODWORD(v7) = v7 + 1;
        }
        ++v11;
        v12 += 104LL;
        v8 = (char *)*((_QWORD *)this + 17);
        v10 = (char *)*((_QWORD *)this + 16);
      }
      while ( v11 != (v8 - v10) / 104 );
      v5 = v26;
    }
    v13 = &v10[104 * (int)v7];
    if ( v13 != v8 )
    {
      std::_Destroy_range<std::allocator<CommandRegistry::ParseRule>>(v13, *((_QWORD *)this + 17));
      *((_QWORD *)this + 17) = v13;
    }
  }
  v14 = *((int *)a2 + 27);
  if ( (_DWORD)v14 != -1 )
  {
    v15 = *((_QWORD *)this + 30) + 4 * v14;
    if ( v15 != *((_QWORD *)this + 31) )
      *((_QWORD *)this + 31) = v15;
    *((_DWORD *)a2 + 27) = -1;
  }
  v16 = *((int *)a2 + 28);
  if ( (_DWORD)v16 != -1 )
  {
    v17 = *((_QWORD *)this + 21) + 12 * v16;
    if ( v17 != *((_QWORD *)this + 22) )
      *((_QWORD *)this + 22) = v17;
    *((_DWORD *)a2 + 28) = -1;
  }
  v36 = 0LL;
  v37 = 0LL;
  v18 = *((_QWORD *)a2 + 9);
  v19 = *((_QWORD *)a2 + 8);
  if ( v19 != v18 )
  {
    v20 = (__int64 *)v37;
    do
    {
      v28 = v19;
      if ( *((__int64 **)&v37 + 1) == v20 )
      {
        std::vector<unsigned __int64>::_Emplace_reallocate<unsigned __int64 const &>(&v36, v20, &v28);
        v20 = (__int64 *)v37;
      }
      else
      {
        *v20++ = v19;
        *(_QWORD *)&v37 = v20;
      }
      v19 += 48LL;
    }
    while ( v19 != v18 );
  }
  *((_DWORD *)a2 + 26) = (*((_DWORD *)this + 34) - *((_DWORD *)this + 32)) / 104;
  CommandRegistry::buildRules((_DWORD)this, (unsigned int)&v26, (_DWORD)a2, (unsigned int)&v36, 0LL);
  if ( (v6 & 0x200000) != 0 )
    v5 = v6;
  v21 = v26;
  v28 = 0x7FFFFFFF00000000LL;
  if ( v26 == 0x100000 )
  {
    v29 = &v32;
    v32 = &std::_Func_impl_no_alloc<CommandRegistry::ParseToken * (*)(CommandRegistry::ParseToken &,CommandRegistry::Symbol),CommandRegistry::ParseToken *,CommandRegistry::ParseToken &,CommandRegistry::Symbol>::`vftable';
    v33 = &CommandRegistry::expand;
    v34 = &v32;
    v26 = v5;
    v30 = 0LL;
    v31 = 0LL;
    std::vector<CommandRegistry::Symbol>::_Range_construct_or_tidy<CommandRegistry::Symbol const *>(&v30, &v26, &v27);
    LODWORD(v29) = 1048631;
    CommandRegistry::addRule((_DWORD)this, (unsigned int)&v29, (unsigned int)&v30, (unsigned int)&v32, v28);
    v22 = v30;
    if ( !v30 )
      goto LABEL_39;
    v23 = 4 * ((__int64)(*((_QWORD *)&v31 + 1) - (_QWORD)v30) >> 2);
    if ( v23 >= 0x1000 )
    {
      v23 += 39LL;
      v22 = (_BYTE *)*((_QWORD *)v30 - 1);
      if ( (unsigned __int64)((_BYTE *)v30 - v22 - 8) > 0x1F )
        _invalid_parameter_noinfo_noreturn();
    }
  }
  else
  {
    v29 = &v32;
    v32 = &std::_Func_impl_no_alloc<CommandRegistry::ParseToken * (*)(CommandRegistry::ParseToken &,CommandRegistry::Symbol),CommandRegistry::ParseToken *,CommandRegistry::ParseToken &,CommandRegistry::Symbol>::`vftable';
    v33 = &CommandRegistry::expand;
    v34 = &v32;
    v26 = v5;
    v27 = v21;
    v30 = 0LL;
    v31 = 0LL;
    std::vector<CommandRegistry::Symbol>::_Range_construct_or_tidy<CommandRegistry::Symbol const *>(&v30, &v26, &v28);
    LODWORD(v29) = 1048631;
    CommandRegistry::addRule((_DWORD)this, (unsigned int)&v29, (unsigned int)&v30, (unsigned int)&v32, v28);
    v22 = v30;
    if ( !v30 )
      goto LABEL_39;
    v23 = 4 * ((__int64)(*((_QWORD *)&v31 + 1) - (_QWORD)v30) >> 2);
    if ( v23 >= 0x1000 )
    {
      v23 += 39LL;
      v22 = (_BYTE *)*((_QWORD *)v30 - 1);
      if ( (unsigned __int64)((_BYTE *)v30 - v22 - 8) > 0x1F )
        _invalid_parameter_noinfo_noreturn();
    }
  }
  operator delete(v22, v23);
LABEL_39:
  v24 = v36;
  if ( v36 )
  {
    v25 = (*((_QWORD *)&v37 + 1) - (_QWORD)v36) & 0xFFFFFFFFFFFFFFF8uLL;
    if ( v25 >= 0x1000 )
    {
      v25 += 39LL;
      v24 = (_BYTE *)*((_QWORD *)v36 - 1);
      if ( (unsigned __int64)((_BYTE *)v36 - v24 - 8) > 0x1F )
        _invalid_parameter_noinfo_noreturn();
    }
    operator delete(v24, v25);
  }
}