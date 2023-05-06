package DataBaseAccess;

import javax.persistence.EntityManager;
import javax.persistence.EntityManagerFactory;
import javax.persistence.Persistence;
import Entity.CameraEntity;
import Entity.CaminEntity;
import Entity.StudentEntity;

public class DBAccess {
    private static EntityManagerFactory entityManagerFactory = Persistence.createEntityManagerFactory("default");
    private static EntityManager entityManager = entityManagerFactory.createEntityManager();

    public static EntityManager getInstance(){
        return entityManager;
    }

    public static void closeConnection(){
        entityManager.close();
        entityManagerFactory.close();
    }
}

