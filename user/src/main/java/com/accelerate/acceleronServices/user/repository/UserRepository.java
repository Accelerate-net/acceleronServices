package com.accelerate.acceleronServices.user.repository;

import com.accelerate.acceleronServices.user.model.UserEntity;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.Modifying;
import org.springframework.data.jpa.repository.Query;
import org.springframework.transaction.annotation.Transactional;

import java.util.List;

public interface UserRepository extends JpaRepository<UserEntity, Integer> {

    @Override
    @Query(value = "select * from z_user ", nativeQuery = true)
    List<UserEntity> findAll();

    @Query(value = "select * from z_user where id = ?1", nativeQuery = true)
    UserEntity getById(int id);

    @Modifying
    @Transactional
    @Query(value = "update z_user set isDeleted = 1 where id = ?1",nativeQuery = true)
    int DeleteById(int id);
}
